<?php
/**
 * seo_audit.php
 * Instant SEO audit tool used on the SEO service detail page.
 * Fetches a visitor-supplied URL server-side and checks common on-page
 * SEO signals (title, meta description, headings, alt text, etc).
 *
 * SSRF hardening: the target host is resolved to an IP and rejected if it
 * is not a public address (blocks localhost, private ranges, link-local /
 * cloud metadata addresses). curl is then pinned to that exact IP via
 * CURLOPT_RESOLVE so a second DNS lookup can't rebind to an internal host
 * after the check passes, and redirects are not followed automatically.
 */

/**
 * Reject a small number of requests per visitor per hour so the tool can't
 * be used to hammer arbitrary third-party sites from this server.
 */
function seo_audit_rate_limit_ok() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $now = time();
    $window = 3600;
    $limit = 20;

    $hits = $_SESSION['seo_audit_hits'] ?? [];
    $hits = array_filter($hits, fn($t) => $t > $now - $window);

    if (count($hits) >= $limit) {
        $_SESSION['seo_audit_hits'] = $hits;
        return false;
    }

    $hits[] = $now;
    $_SESSION['seo_audit_hits'] = $hits;
    return true;
}

/**
 * Validate a visitor-supplied URL and resolve it to a public IP.
 * Returns ['ok' => true, 'url', 'host', 'ip', 'port', 'scheme'] or ['ok' => false, 'error'].
 */
function validate_audit_url($rawUrl) {
    $rawUrl = trim((string) $rawUrl);
    if ($rawUrl === '') {
        return ['ok' => false, 'error' => 'Please enter a website URL.'];
    }
    if (!preg_match('#^https?://#i', $rawUrl)) {
        $rawUrl = 'http://' . $rawUrl;
    }

    $parts = parse_url($rawUrl);
    $scheme = strtolower($parts['scheme'] ?? '');
    $host = $parts['host'] ?? '';

    if (!$parts || $host === '' || !in_array($scheme, ['http', 'https'], true)) {
        return ['ok' => false, 'error' => 'That does not look like a valid website URL.'];
    }

    if (strtolower($host) === 'localhost' || str_ends_with(strtolower($host), '.local')) {
        return ['ok' => false, 'error' => 'That URL cannot be audited.'];
    }

    // Literal IP in the URL? Validate it directly instead of resolving.
    if (filter_var($host, FILTER_VALIDATE_IP)) {
        if (!filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return ['ok' => false, 'error' => 'That URL points to a restricted address and cannot be audited.'];
        }
        $ip = $host;
    } else {
        $records = @dns_get_record($host, DNS_A) ?: [];
        $ip = $records[0]['ip'] ?? null;
        if (!$ip) {
            $resolved = gethostbyname($host);
            $ip = ($resolved !== $host) ? $resolved : null;
        }
        if (!$ip) {
            return ['ok' => false, 'error' => 'Could not resolve that domain name.'];
        }
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return ['ok' => false, 'error' => 'That URL points to a restricted address and cannot be audited.'];
        }
    }

    $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);

    return [
        'ok' => true,
        'url' => $rawUrl,
        'host' => $host,
        'ip' => $ip,
        'port' => $port,
        'scheme' => $scheme,
    ];
}

/**
 * Fetch the page with curl, pinned to the pre-validated IP.
 * Returns ['ok' => true, 'body', 'status', 'time'] or ['ok' => false, 'error'].
 */
function fetch_url_for_audit($validated) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $validated['url'],
        CURLOPT_RESOLVE => ["{$validated['host']}:{$validated['port']}:{$validated['ip']}"],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false, // avoid redirect-based SSRF bypass
        CURLOPT_TIMEOUT => 8,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_CAINFO => __DIR__ . '/cacert.pem', // bundled so this works even if the server's php.ini has no CA path set
        CURLOPT_USERAGENT => 'OpportuneXSEOAuditBot/1.0 (+instant site audit tool)',
        CURLOPT_RANGE => '0-2097151', // cap at ~2MB of response body
    ]);

    $start = microtime(true);
    $body = curl_exec($ch);
    $loadTime = microtime(true) - $start;
    $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($body === false) {
        return ['ok' => false, 'error' => 'Could not reach that website (' . $err . ').'];
    }
    if ($status >= 300 && $status < 400) {
        return ['ok' => false, 'error' => 'That URL redirects elsewhere. Please enter the final destination URL directly.'];
    }
    if ($status >= 400) {
        return ['ok' => false, 'error' => "That website returned an error (HTTP $status)."];
    }

    return ['ok' => true, 'body' => $body, 'status' => $status, 'time' => $loadTime];
}

/**
 * Judge whether a title tag reads as attractive/click-worthy, not just
 * whether it hits a character count. Looks for the same hooks copywriters
 * use to earn a click in search results: numbers, power words, a question,
 * or a clear brand/topic separator — and flags titles that are just a
 * plain, generic label.
 *
 * Returns ['level' => pass|warn|fail, 'detail' => string, 'penalty' => int].
 */
function assess_title_attractiveness($title) {
    $reasons = [];
    $hooks = 0;

    if (preg_match('/\d/', $title)) {
        $hooks++;
        $reasons[] = 'includes a number, which tends to draw more clicks';
    }

    $powerWords = ['free', 'best', 'top', 'new', 'ultimate', 'exclusive', 'proven', 'secret',
        'guide', 'easy', 'essential', 'amazing', 'discover', 'unlock', 'expert', 'trusted',
        'affordable', 'professional', 'custom', 'fast', 'guaranteed', 'award'];
    $lowerTitle = strtolower($title);
    $matchedWord = null;
    foreach ($powerWords as $word) {
        if (str_contains($lowerTitle, $word)) {
            $matchedWord = $word;
            break;
        }
    }
    if ($matchedWord) {
        $hooks++;
        $reasons[] = "uses the compelling word \"$matchedWord\"";
    }

    if (str_contains($title, '?')) {
        $hooks++;
        $reasons[] = 'poses a question, which sparks curiosity';
    }

    if (preg_match('/\s[\|\-–—:]\s/', $title)) {
        $hooks++;
        $reasons[] = 'clearly separates the topic from the brand name';
    }

    $wordCount = str_word_count($title);

    if ($hooks >= 2) {
        return [
            'level' => 'pass',
            'penalty' => 0,
            'detail' => 'Attractive — it ' . implode(' and ', $reasons) . '.',
        ];
    }

    if ($hooks === 1) {
        return [
            'level' => 'warn',
            'penalty' => 5,
            'detail' => 'Decent, but plain — it ' . $reasons[0] . ', but adding one more hook (a number, a question, or a power word) would make it stand out more in search results.',
        ];
    }

    if ($wordCount <= 3) {
        return [
            'level' => 'fail',
            'penalty' => 12,
            'detail' => "reads as a generic label rather than something someone would want to click. Try adding a benefit, a number, or a question to make it more inviting.",
        ];
    }

    return [
        'level' => 'warn',
        'penalty' => 8,
        'detail' => "is descriptive but plain — it doesn't use any of the usual hooks (numbers, questions, power words) that draw clicks in search results.",
    ];
}

/**
 * Parse the HTML and run a set of on-page SEO checks.
 * Returns ['score' => int, 'checks' => [ ['label','level' => pass|warn|fail,'detail'] ]].
 */
function analyze_seo_html($html, $loadTime, $scheme) {
    $checks = [];
    $score = 100;

    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);

    // Title tag
    $titleNode = $dom->getElementsByTagName('title')->item(0);
    $title = $titleNode ? trim($titleNode->textContent) : '';
    if ($title === '') {
        $checks[] = ['label' => 'Title tag', 'level' => 'fail', 'detail' => 'Missing entirely. Every page needs a unique <title>.'];
        $score -= 15;
    } else {
        $review = assess_title_attractiveness($title);
        $checks[] = ['label' => 'Title tag', 'level' => $review['level'], 'detail' => '"' . $title . '" — ' . $review['detail']];
        $score -= $review['penalty'];
    }

    // Meta description
    $metaDesc = '';
    foreach ($xpath->query('//meta[@name="description"]') as $node) {
        $metaDesc = trim($node->getAttribute('content'));
        break;
    }
    if ($metaDesc === '') {
        $checks[] = ['label' => 'Meta description', 'level' => 'fail', 'detail' => 'Missing. Search engines will auto-generate a snippet instead.'];
        $score -= 10;
    } elseif (strlen($metaDesc) < 70 || strlen($metaDesc) > 160) {
        $checks[] = ['label' => 'Meta description', 'level' => 'warn', 'detail' => strlen($metaDesc) . ' characters — aim for 70-160.'];
        $score -= 5;
    } else {
        $checks[] = ['label' => 'Meta description', 'level' => 'pass', 'detail' => 'Good length (' . strlen($metaDesc) . ' characters).'];
    }

    // H1 usage
    $h1s = $dom->getElementsByTagName('h1');
    if ($h1s->length === 0) {
        $checks[] = ['label' => 'H1 heading', 'level' => 'fail', 'detail' => 'No H1 found on the page.'];
        $score -= 10;
    } elseif ($h1s->length > 1) {
        $checks[] = ['label' => 'H1 heading', 'level' => 'warn', 'detail' => $h1s->length . ' H1 tags found — ideally there is exactly one per page.'];
        $score -= 5;
    } else {
        $checks[] = ['label' => 'H1 heading', 'level' => 'pass', 'detail' => 'Exactly one H1 found.'];
    }

    // Image alt text
    $images = $dom->getElementsByTagName('img');
    $missingAlt = 0;
    foreach ($images as $img) {
        if (trim($img->getAttribute('alt')) === '') {
            $missingAlt++;
        }
    }
    if ($images->length > 0 && $missingAlt > 0) {
        $level = $missingAlt === $images->length ? 'fail' : 'warn';
        $checks[] = ['label' => 'Image alt text', 'level' => $level, 'detail' => "$missingAlt of {$images->length} images are missing alt text."];
        $score -= ($level === 'fail') ? 10 : 5;
    } else {
        $checks[] = ['label' => 'Image alt text', 'level' => 'pass', 'detail' => $images->length > 0 ? 'All images have alt text.' : 'No images on this page.'];
    }

    // Viewport / mobile-friendly
    $hasViewport = $xpath->query('//meta[@name="viewport"]')->length > 0;
    if (!$hasViewport) {
        $checks[] = ['label' => 'Mobile viewport tag', 'level' => 'fail', 'detail' => 'No viewport meta tag — the page likely is not mobile-friendly.'];
        $score -= 10;
    } else {
        $checks[] = ['label' => 'Mobile viewport tag', 'level' => 'pass', 'detail' => 'Viewport meta tag present.'];
    }

    // Canonical tag
    $hasCanonical = $xpath->query('//link[@rel="canonical"]')->length > 0;
    if (!$hasCanonical) {
        $checks[] = ['label' => 'Canonical tag', 'level' => 'warn', 'detail' => 'No canonical link tag found.'];
        $score -= 5;
    } else {
        $checks[] = ['label' => 'Canonical tag', 'level' => 'pass', 'detail' => 'Canonical link tag present.'];
    }

    // HTTPS
    if ($scheme !== 'https') {
        $checks[] = ['label' => 'HTTPS', 'level' => 'fail', 'detail' => 'Site is served over plain HTTP, which search engines penalize.'];
        $score -= 10;
    } else {
        $checks[] = ['label' => 'HTTPS', 'level' => 'pass', 'detail' => 'Served securely over HTTPS.'];
    }

    // Content length (thin content check)
    $bodyNode = $dom->getElementsByTagName('body')->item(0);
    $text = $bodyNode ? preg_replace('/\s+/', ' ', trim($bodyNode->textContent)) : '';
    $wordCount = $text === '' ? 0 : str_word_count($text);
    if ($wordCount < 300) {
        $checks[] = ['label' => 'Content length', 'level' => 'warn', 'detail' => "Only about $wordCount words of visible text — thin content can rank poorly."];
        $score -= 5;
    } else {
        $checks[] = ['label' => 'Content length', 'level' => 'pass', 'detail' => "About $wordCount words of visible text."];
    }

    // Load time
    if ($loadTime > 2.5) {
        $checks[] = ['label' => 'Response time', 'level' => 'warn', 'detail' => round($loadTime, 2) . 's to respond — aim under 2.5s.'];
        $score -= 5;
    } else {
        $checks[] = ['label' => 'Response time', 'level' => 'pass', 'detail' => round($loadTime, 2) . 's to respond.'];
    }

    return ['score' => max(0, $score), 'checks' => $checks];
}

/**
 * Run the full audit pipeline for a visitor-supplied URL.
 * Returns ['ok' => true, 'url', 'score', 'checks'] or ['ok' => false, 'error'].
 */
function run_seo_audit($rawUrl) {
    $validated = validate_audit_url($rawUrl);
    if (!$validated['ok']) {
        return $validated;
    }

    $fetch = fetch_url_for_audit($validated);
    if (!$fetch['ok']) {
        return $fetch;
    }

    $result = analyze_seo_html($fetch['body'], $fetch['time'], $validated['scheme']);
    return [
        'ok' => true,
        'url' => $validated['url'],
        'score' => $result['score'],
        'checks' => $result['checks'],
    ];
}
