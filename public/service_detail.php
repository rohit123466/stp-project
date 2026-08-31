<?php
/**
 * service_detail.php - full page for a single service, linked from the
 * service cards on index.php and services.php.
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$service = null;

if ($id) {
    $stmt = mysqli_prepare($conn, "SELECT id, title, description, image, meta_title, meta_description FROM services WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $service = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

$pageTitle = $service ? $service['meta_title'] : 'Service Not Found - OpportuneX Digital';
$metaDescription = $service ? $service['meta_description'] : 'The requested service could not be found.';

require_once __DIR__ . '/../includes/header.php';
?>

<section class="py-5">
  <div class="container">
    <?php if ($service): ?>
      <div class="row justify-content-center align-items-start g-5">
        <div class="col-lg-5">
          <img src="<?= service_hero_image_url($service['image'], $service['title']) ?>" class="img-fluid rounded" alt="<?= clean_input($service['title']) ?>">
        </div>
        <div class="col-lg-7">
          <h1 class="fw-bold mb-3"><?= clean_input($service['title']) ?></h1>
          <p class="lead text-muted"><?= nl2br(clean_input($service['description'])) ?></p>
          <a href="contact.php" class="btn btn-warning btn-lg mt-3">Get a Free Consultation</a>
          <a href="services.php" class="btn btn-outline-dark btn-lg mt-3">Back to All Services</a>
        </div>
      </div>
    <?php else: ?>
      <div class="text-center">
        <h1 class="fw-bold">Service Not Found</h1>
        <p class="text-muted">The service you're looking for doesn't exist or may have been removed.</p>
        <a href="services.php" class="btn btn-outline-dark mt-3">View All Services</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
