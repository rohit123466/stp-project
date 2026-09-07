<?php
/**
 * header.php
 * Shared header for all PUBLIC-facing pages.
 * Expects the including page to optionally set:
 *   $pageTitle        - <title> text
 *   $metaDescription  - meta description content
 * before including this file.
 */
if (!isset($pageTitle)) {
    $pageTitle = SITE_NAME . ' - Digital Marketing Agency';
}
if (!isset($metaDescription)) {
    $metaDescription = 'Grovix Digital is a full-service digital marketing agency helping businesses grow online.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= clean_input($pageTitle) ?></title>
<meta name="description" content="<?= clean_input($metaDescription) ?>">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/public/index.php">Grovix <span class="text-warning">Digital</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/public/index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/public/about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/public/services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/public/portfolio.php">Portfolio</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/public/contact.php">Contact</a></li>
      </ul>
    </div>
  </div>
</nav>
