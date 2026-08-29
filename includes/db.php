<?php
/**
 * db.php
 * Opens a single mysqli connection used by every page.
 * All queries elsewhere use prepared statements against this $conn object.
 */

require_once __DIR__ . '/../config/config.php';

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
