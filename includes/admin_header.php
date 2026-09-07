<?php
/**
 * admin_header.php
 * Shared layout (sidebar + top bar) for all admin panel pages.
 * Expects auth.php to already have been included by the calling page,
 * and optionally $pageTitle to be set.
 */
if (!isset($pageTitle)) {
    $pageTitle = 'Admin Panel';
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?> - Grovix Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="d-flex">
  <nav class="admin-sidebar p-3" style="width: 240px;">
    <h5 class="text-white mb-4">Grovix <span class="text-warning">Admin</span></h5>
    <ul class="nav nav-pills flex-column gap-1">
      <li class="nav-item"><a class="nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link <?= $currentPage === 'manage_services.php' ? 'active' : '' ?>" href="manage_services.php">Services</a></li>
      <li class="nav-item"><a class="nav-link <?= $currentPage === 'manage_portfolio.php' ? 'active' : '' ?>" href="manage_portfolio.php">Portfolio</a></li>
      <li class="nav-item"><a class="nav-link <?= $currentPage === 'manage_testimonials.php' ? 'active' : '' ?>" href="manage_testimonials.php">Testimonials</a></li>
      <li class="nav-item"><a class="nav-link <?= $currentPage === 'view_leads.php' ? 'active' : '' ?>" href="view_leads.php">Leads</a></li>
      <li class="nav-item"><a class="nav-link <?= $currentPage === 'change_password.php' ? 'active' : '' ?>" href="change_password.php">Change Password</a></li>
      <li class="nav-item mt-3"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
    </ul>
  </nav>

  <main class="flex-grow-1 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="fw-bold mb-0"><?= htmlspecialchars($pageTitle) ?></h3>
      <span class="text-muted small">Logged in as <?= htmlspecialchars($_SESSION['admin_username'] ?? 'admin') ?></span>
    </div>
