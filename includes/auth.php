<?php
/**
 * auth.php
 * Include this at the very top of every protected admin page.
 * It starts the session (if not already started) and redirects
 * back to the login page if the admin is not logged in.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
