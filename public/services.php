<?php
/**
 * services.php - full grid of services pulled from the database
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Our Services - Grovix Digital';
$metaDescription = 'Explore the full range of digital marketing services offered by Grovix Digital: SEO, social media, web design, and PPC.';

require_once __DIR__ . '/../includes/header.php';

$services = [];
$result = mysqli_query($conn, "SELECT id, title, description, image FROM services ORDER BY created_at DESC");
while ($row = mysqli_fetch_assoc($result)) {
    $services[] = $row;
}
?>

<section class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <h1 class="fw-bold">Our Services</h1>
      <p class="text-muted">Everything you need to grow your business online, under one roof.</p>
    </div>

    <div class="row g-4">
      <?php foreach ($services as $service): ?>
        <div class="col-md-6 col-lg-4">
          <a href="service_detail.php?id=<?= (int)$service['id'] ?>" class="text-decoration-none text-reset">
            <div class="card card-service h-100">
              <img src="<?= service_image_url($service['image'], $service['title']) ?>" class="card-img-top" alt="<?= clean_input($service['title']) ?>">
              <div class="card-body">
                <h5 class="card-title"><?= clean_input($service['title']) ?></h5>
                <p class="card-text text-muted"><?= clean_input($service['description']) ?></p>
              </div>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
      <?php if (empty($services)): ?>
        <p class="text-center text-muted">No services added yet. Please check back soon.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
