<?php
/**
 * web_health_check.php
 * Instant website health check + accessibility contrast checker used on
 * the Website Design & Development service detail page.
 *
 * The health check reuses the same SSRF-hardened fetch pipeline as
 * seo_audit.php (validate_audit_url / fetch_url_for_audit) but grades a
 * different set of signals: page-load UX and mobile-friendliness rather
 * than search-ranking factors.
 */

/**
 * Parse the HTML and run a set of website-health checks.
 * Returns ['score' => int, 'checks' => [ ['label','level' => pass|warn|fail,'detail'] ]].
 */
function analyze_web_health_html($html, $loadTime, $scheme) {
    $checks = [];
    $score = 100;

    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);

    // HTTPS
    if ($scheme !== 'https') {
        $checks[] = ['label' => 'HTTPS', 'level' => 'fail', 'detail' => 'Site is served over plain HTTP — visitors will see a "Not Secure" warning in their browser.'];
        $score -= 15;
    } else {
        $checks[] = ['label' => 'HTTPS', 'level' => 'pass', 'detail' => 'Served securely over HTTPS.'];
    }

    // DOCTYPE
    $hasDoctype = (bool) preg_match('/^\s*<!doctype\s+html/i', $html);
    if (!$hasDoctype) {
        $checks[] = ['label' => 'HTML5 doctype', 'level' => 'warn', 'detail' => 'No <!DOCTYPE html> declaration found — this can trigger inconsistent rendering across browsers.'];
        $score -= 5;
    } else {
        $checks[] = ['label' => 'HTML5 doctype', 'level' => 'pass', 'detail' => 'Modern HTML5 doctype declared.'];
    }

    // Mobile viewport
    $hasViewport = $xpath->query('//meta[@name="viewport"]')->length > 0;
    if (!$hasViewport) {
        $checks[] = ['label' => 'Mobile-friendly', 'level' => 'fail', 'detail' => 'No viewport meta tag — the page will likely render as a tiny, unreadable desktop layout on phones.'];
        $score -= 20;
    } else {
        $checks[] = ['label' => 'Mobile-friendly', 'level' => 'pass', 'detail' => 'Viewport meta tag present, so the page can adapt to phone screens.'];
    }

    // Favicon
    $hasFavicon = $xpath->query('//link[@rel="icon" or @rel="shortcut icon"]')->length > 0;
    if (!$hasFavicon) {
        $checks[] = ['label' => 'Favicon', 'level' => 'warn', 'detail' => 'No favicon found — the browser tab will show a generic blank icon.'];
        $score -= 5;
    } else {
        $checks[] = ['label' => 'Favicon', 'level' => 'pass', 'detail' => 'Favicon is set.'];
    }

    // Page weight
    $sizeKb = strlen($html) / 1024;
    if ($sizeKb > 1024) {
        $checks[] = ['label' => 'Page weight', 'level' => 'warn', 'detail' => round($sizeKb) . 'KB of HTML — heavier pages take longer to load, especially on mobile data.'];
        $score -= 10;
    } else {
        $checks[] = ['label' => 'Page weight', 'level' => 'pass', 'detail' => round($sizeKb) . 'KB of HTML.'];
    }

    // Render-blocking resources
    $externalCss = $xpath->query('//link[@rel="stylesheet"]')->length;
    $blockingScripts = 0;
    foreach ($xpath->query('//script[@src]') as $script) {
        if (!$script->hasAttribute('async') && !$script->hasAttribute('defer')) {
            $blockingScripts++;
        }
    }
    $blockingTotal = $externalCss + $blockingScripts;
    if ($blockingTotal > 10) {
        $checks[] = ['label' => 'Render-blocking resources', 'level' => 'warn', 'detail' => "$blockingTotal external stylesheets/scripts load before the page can render — consider bundling or adding async/defer."];
        $score -= 10;
    } else {
        $checks[] = ['label' => 'Render-blocking resources', 'level' => 'pass', 'detail' => "$blockingTotal external stylesheet(s)/script(s) — a reasonable amount."];
    }

    // Images missing explicit dimensions (causes layout shift while loading)
    $images = $dom->getElementsByTagName('img');
    $missingDims = 0;
    foreach ($images as $img) {
        if ($img->getAttribute('width') === '' || $img->getAttribute('height') === '') {
            $missingDims++;
        }
    }
    if ($images->length > 0 && $missingDims > 0) {
        $checks[] = ['label' => 'Image layout shift', 'level' => 'warn', 'detail' => "$missingDims of {$images->length} images have no width/height set, which can cause the page to jump around while loading."];
        $score -= 5;
    } else {
        $checks[] = ['label' => 'Image layout shift', 'level' => 'pass', 'detail' => $images->length > 0 ? 'All images specify their dimensions.' : 'No images on this page.'];
    }

    // Load time
    if ($loadTime > 2) {
        $checks[] = ['label' => 'Response time', 'level' => 'warn', 'detail' => round($loadTime, 2) . 's to respond — visitors tend to abandon pages slower than 2-3s.'];
        $score -= 10;
    } else {
        $checks[] = ['label' => 'Response time', 'level' => 'pass', 'detail' => round($loadTime, 2) . 's to respond.'];
    }

    return ['score' => max(0, $score), 'checks' => $checks];
}

/**
 * Run the full website health check pipeline for a visitor-supplied URL.
 * Returns ['ok' => true, 'url', 'score', 'checks'] or ['ok' => false, 'error'].
 */
function run_web_health_check($rawUrl) {
    $validated = validate_audit_url($rawUrl);
    if (!$validated['ok']) {
        return $validated;
    }

    $fetch = fetch_url_for_audit($validated);
    if (!$fetch['ok']) {
        return $fetch;
    }

    $result = analyze_web_health_html($fetch['body'], $fetch['time'], $validated['scheme']);
    return [
        'ok' => true,
        'url' => $validated['url'],
        'score' => $result['score'],
        'checks' => $result['checks'],
    ];
}

/**
 * Parse a hex color string ("#rgb", "#rrggbb", with or without "#") into [r, g, b].
 * Returns null if the string isn't a valid hex color.
 */
function parse_hex_color($hex) {
    $hex = ltrim(trim($hex), '#');
    if (preg_match('/^([0-9a-f]{3})$/i', $hex)) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    if (!preg_match('/^[0-9a-f]{6}$/i', $hex)) {
        return null;
    }
    return [
        hexdec(substr($hex, 0, 2)),
        hexdec(substr($hex, 2, 2)),
        hexdec(substr($hex, 4, 2)),
    ];
}

/**
 * WCAG relative luminance of an sRGB color.
 */
function relative_luminance($r, $g, $b) {
    [$r, $g, $b] = array_map(function ($c) {
        $c /= 255;
        return $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
    }, [$r, $g, $b]);

    return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
}

/**
 * Check two hex colors for WCAG contrast compliance.
 * Returns ['ok' => true, 'ratio', 'aa_normal', 'aa_large', 'aaa_normal', 'aaa_large'] or ['ok' => false, 'error'].
 */
function check_color_contrast($hexA, $hexB) {
    $rgbA = parse_hex_color($hexA);
    $rgbB = parse_hex_color($hexB);

    if ($rgbA === null || $rgbB === null) {
        return ['ok' => false, 'error' => 'Please enter both colors as valid hex codes, e.g. #333333.'];
    }

    $lumA = relative_luminance(...$rgbA);
    $lumB = relative_luminance(...$rgbB);
    $lighter = max($lumA, $lumB);
    $darker = min($lumA, $lumB);
    $ratio = ($lighter + 0.05) / ($darker + 0.05);

    return [
        'ok' => true,
        'ratio' => $ratio,
        'aa_normal' => $ratio >= 4.5,
        'aa_large' => $ratio >= 3,
        'aaa_normal' => $ratio >= 7,
        'aaa_large' => $ratio >= 4.5,
    ];
}
