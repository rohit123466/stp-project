<?php
/**
 * seed_admin.php
 * One-time setup script: sets the default admin password using PHP's
 * own password_hash() so the hash always matches your PHP install.
 *
 * Run this ONCE in your browser after importing schema.sql, e.g.:
 *   http://localhost/grovix/sql/seed_admin.php
 *
 * Default login created: username = admin, password = admin123
 *
 * IMPORTANT: Delete this file after running it once (see README).
 */
require_once __DIR__ . '/../includes/db.php';

$username = 'admin';
$plainPassword = 'admin123';
$hash = password_hash($plainPassword, PASSWORD_DEFAULT);

$stmt = mysqli_prepare($conn, "SELECT id FROM admins WHERE username = ?");
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$exists = mysqli_stmt_get_result($stmt)->fetch_assoc();
mysqli_stmt_close($stmt);

if ($exists) {
    $stmt = mysqli_prepare($conn, "UPDATE admins SET password = ? WHERE username = ?");
    mysqli_stmt_bind_param($stmt, 'ss', $hash, $username);
} else {
    $stmt = mysqli_prepare($conn, "INSERT INTO admins (username, password) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, 'ss', $username, $hash);
}
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

echo "Admin account ready.<br>Username: admin<br>Password: admin123<br><br>";
echo "<strong>Please delete sql/seed_admin.php now for security.</strong>";
