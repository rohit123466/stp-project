<?php
/**
 * config.php
 * All site-wide configuration lives here, separated from application logic.
 * Edit these values to match your XAMPP / phpMyAdmin setup.
 */

// --- Database credentials -----------------------------------------
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'Rohit@1234');
define('DB_NAME', 'opportunex_cms');

// --- Site settings ---------------------------------------------------
define('SITE_NAME', 'OpportuneX Digital');

// Base URL of the project as seen in the browser, no trailing slash.
// Running via PHP's built-in dev server (php -S localhost:8000) with the
// project root as the document root, so this points straight at that.
// If you later move this to XAMPP's htdocs, change this back to something
// like http://localhost/opportunex and DB_PASS back to '' (or your XAMPP
// MySQL password).
define('BASE_URL', 'http://localhost:8000');

// --- Upload settings ---------------------------------------------------
define('UPLOAD_DIR', __DIR__ . '/../uploads/'); // server filesystem path
define('UPLOAD_URL', BASE_URL . '/uploads/');    // browser-facing URL
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024);      // 2MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('ALLOWED_IMAGE_EXT', ['jpg', 'jpeg', 'png', 'webp']);
