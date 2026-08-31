<?php
/**
 * functions.php
 * Small reusable helper functions shared by the public site and admin panel.
 */

/**
 * Trim and HTML-escape a string coming from user input.
 * Used both to clean data before saving and before echoing it back out.
 */
function clean_input($value) {
    return htmlspecialchars(trim($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Basic email format validation.
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Handle a single image upload from $_FILES.
 * Validates real MIME type + extension + size, then moves the file
 * into /uploads with a unique name (timestamp + original name).
 *
 * @param string $inputName name of the <input type="file"> field
 * @return array ['ok' => bool, 'filename' => string|null, 'error' => string|null]
 */
function handle_image_upload($inputName) {
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['ok' => true, 'filename' => null, 'error' => null]; // no file chosen, not an error
    }

    $file = $_FILES[$inputName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'filename' => null, 'error' => 'Upload failed. Please try again.'];
    }

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['ok' => false, 'filename' => null, 'error' => 'Image must be smaller than 2MB.'];
    }

    // Check the ACTUAL file content type on the server, not the browser-supplied one.
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES, true)) {
        return ['ok' => false, 'filename' => null, 'error' => 'Only JPG, PNG, or WEBP images are allowed.'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_IMAGE_EXT, true)) {
        return ['ok' => false, 'filename' => null, 'error' => 'Invalid file extension.'];
    }

    // Unique filename: timestamp + sanitized original name
    $safeOriginal = preg_replace('/[^A-Za-z0-9_\-.]/', '_', basename($file['name']));
    $newFilename = time() . '_' . $safeOriginal;
    $destination = UPLOAD_DIR . $newFilename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['ok' => false, 'filename' => null, 'error' => 'Could not save the uploaded file.'];
    }

    return ['ok' => true, 'filename' => $newFilename, 'error' => null];
}

/**
 * Build the public URL for an uploaded image, falling back to a placeholder.
 */
function image_url($filename) {
    if (empty($filename) || !file_exists(UPLOAD_DIR . $filename)) {
        return BASE_URL . '/assets/img/placeholder.svg';
    }
    return UPLOAD_URL . $filename;
}

/**
 * Build the image URL for a service card. Falls back to a branded,
 * icon-specific placeholder matched by keywords in the service title
 * (instead of the generic placeholder) when no image has been uploaded.
 */
function service_image_url($filename, $title = '') {
    if (!empty($filename) && file_exists(UPLOAD_DIR . $filename)) {
        return UPLOAD_URL . $filename;
    }

    $title = strtolower($title);
    if (str_contains($title, 'seo') || str_contains($title, 'search engine')) {
        return BASE_URL . '/assets/img/placeholder-seo.svg';
    }
    if (str_contains($title, 'social media') || str_contains($title, 'smm')) {
        return BASE_URL . '/assets/img/placeholder-smm.svg';
    }
    if (str_contains($title, 'web design') || str_contains($title, 'web development') || str_contains($title, 'website')) {
        return BASE_URL . '/assets/img/placeholder-webdev.svg';
    }
    if (str_contains($title, 'pay-per-click') || str_contains($title, 'ppc') || str_contains($title, 'advertising')) {
        return BASE_URL . '/assets/img/placeholder-ppc.svg';
    }

    return BASE_URL . '/assets/img/placeholder.svg';
}

/**
 * Build the image URL for a portfolio card. Falls back to the generic
 * placeholder, except for specific known projects that get their own
 * branded illustration instead (matched by title).
 */
function portfolio_image_url($filename, $title = '') {
    if (!empty($filename) && file_exists(UPLOAD_DIR . $filename)) {
        return UPLOAD_URL . $filename;
    }

    if (str_contains(strtolower($title), 'moviefy')) {
        return BASE_URL . '/assets/img/portfolio-moviefy.svg';
    }
    if (str_contains(strtolower($title), 'ipl') || str_contains(strtolower($title), 'auction')) {
        return BASE_URL . '/assets/img/portfolio-iplauction.svg';
    }

    return BASE_URL . '/assets/img/placeholder.svg';
}

/**
 * Build the hero banner image URL for a single service's detail page.
 * Falls back to an illustration of people doing that work (instead of the
 * plain icon used on the listing cards) when no image has been uploaded.
 */
function service_hero_image_url($filename, $title = '') {
    if (!empty($filename) && file_exists(UPLOAD_DIR . $filename)) {
        return UPLOAD_URL . $filename;
    }

    $title = strtolower($title);
    if (str_contains($title, 'seo') || str_contains($title, 'search engine')) {
        return BASE_URL . '/assets/img/service-hero-seo.svg';
    }
    if (str_contains($title, 'social media') || str_contains($title, 'smm')) {
        return BASE_URL . '/assets/img/service-hero-smm.svg';
    }
    if (str_contains($title, 'web design') || str_contains($title, 'web development') || str_contains($title, 'website')) {
        return BASE_URL . '/assets/img/service-hero-webdev.svg';
    }
    if (str_contains($title, 'pay-per-click') || str_contains($title, 'ppc') || str_contains($title, 'advertising')) {
        return BASE_URL . '/assets/img/service-hero-ppc.svg';
    }

    return BASE_URL . '/assets/img/placeholder.svg';
}

/**
 * Bootstrap badge color class for a lead status, used in admin tables.
 */
function status_badge_class($status) {
    $map = [
        'New'        => 'bg-primary',
        'Contacted'  => 'bg-info text-dark',
        'Interested' => 'bg-warning text-dark',
        'Converted'  => 'bg-success',
        'Lost'       => 'bg-secondary',
    ];
    return $map[$status] ?? 'bg-secondary';
}
