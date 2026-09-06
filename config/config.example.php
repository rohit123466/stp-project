<?php
/**
 * config.example.php
 * Copy this file to config.php and fill in your real values.
 * config.php is gitignored and must never be committed.
 */

// --- Database credentials -----------------------------------------
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'opportunex_cms');

// --- Site settings ---------------------------------------------------
define('SITE_NAME', 'OpportuneX Digital');

// Base URL of the project as seen in the browser, no trailing slash.
define('BASE_URL', 'http://localhost/opportunex');

// --- Upload settings ---------------------------------------------------
define('UPLOAD_DIR', __DIR__ . '/../uploads/'); // server filesystem path
define('UPLOAD_URL', BASE_URL . '/uploads/');    // browser-facing URL
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024);      // 2MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('ALLOWED_IMAGE_EXT', ['jpg', 'jpeg', 'png', 'webp']);
