<?php
/**
 * logout.php
 * Destroys the admin session and returns to the login page.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;
