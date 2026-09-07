<?php
/**
 * index.php - Homepage
 * Pulls a few services and all testimonials dynamically from the database.
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'GroviX Digital - Grow Your Business Online';
$metaDescription = 'GroviX Digital is a full-service digital marketing agency offering SEO, social media, web design, and PPC services.';

require_once __DIR__ . '/../includes/header.php';

// Fetch a handful of services for the homepage preview
$services = [];
$result = mysqli_query($conn, "SELECT id, title, description, image FROM services ORDER BY created_at DESC LIMIT 4");
while ($row = mysqli_fetch_assoc($result)) {
    $services[] = $row;
}

// Fetch testimonials
$testimonials = [];
$result = mysqli_query($conn, "SELECT client_name, message, rating, image FROM testimonials ORDER BY created_at DESC LIMIT 6");
while ($row = mysqli_fetch_assoc($result)) {
    $testimonials[] = $row;
}
?>

<section class="hero text-center">
  <div class="container">
    <h1 class="display-5 fw-bold">Digital Marketing That Actually Grows Your Business</h1>
    <p class="lead col-lg-8 mx-auto mt-3">
      GroviX Digital combines SEO, social media, web design, and paid advertising
      into one results-driven strategy built around your goals.
    </p>
    <a href="contact.php" class="btn btn-warning btn-lg mt-3">Get a Free Consultation</a>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold">Our Services</h2>
      <p class="text-muted">A snapshot of what we do best.</p>
    </div>
    <div class="row g-4">
      <?php foreach ($services as $service): ?>
        <div class="col-md-6 col-lg-3">
          <a href="service_detail.php?id=<?= (int)$service['id'] ?>" class="text-decoration-none text-reset">
            <div class="card card-service h-100">
              <img src="<?= service_image_url($service['image'], $service['title']) ?>" class="card-img-top" alt="<?= clean_input($service['title']) ?>">
              <div class="card-body">
                <h5 class="card-title"><?= clean_input($service['title']) ?></h5>
                <p class="card-text small text-muted"><?= clean_input(mb_strimwidth($service['description'], 0, 100, '...')) ?></p>
              </div>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
      <?php if (empty($services)): ?>
        <p class="text-center text-muted">Services coming soon.</p>
      <?php endif; ?>
    </div>
    <div class="text-center mt-4">
      <a href="services.php" class="btn btn-outline-dark">View All Services</a>
    </div>
  </div>
</section>

<section class="py-5 bg-white">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold">What Our Clients Say</h2>
    </div>
    <div class="row g-4">
      <?php foreach ($testimonials as $t): ?>
        <div class="col-md-6 col-lg-4">
          <div class="card card-testimonial p-3">
            <div class="card-body">
              <div class="rating-stars mb-2">
                <?= str_repeat('&#9733;', (int)$t['rating']) . str_repeat('&#9734;', 5 - (int)$t['rating']) ?>
              </div>
              <p class="card-text fst-italic">"<?= clean_input($t['message']) ?>"</p>
              <p class="fw-bold mb-0">- <?= clean_input($t['client_name']) ?></p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (empty($testimonials)): ?>
        <p class="text-center text-muted">No testimonials yet.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
