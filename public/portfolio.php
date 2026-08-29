<?php
/**
 * portfolio.php - grid of past client work pulled from the database
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Our Portfolio - OpportuneX Digital';
$metaDescription = 'See the results OpportuneX Digital has delivered for clients across industries.';

require_once __DIR__ . '/../includes/header.php';

$items = [];
$result = mysqli_query($conn, "SELECT id, title, description, image, client_name FROM portfolio ORDER BY created_at DESC");
while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
}
?>

<section class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <h1 class="fw-bold">Our Portfolio</h1>
      <p class="text-muted">A look at the work we're proud of.</p>
    </div>

    <div class="row g-4">
      <?php foreach ($items as $item): ?>
        <div class="col-md-6 col-lg-4">
          <div class="card card-portfolio">
            <img src="<?= image_url($item['image']) ?>" class="card-img-top" alt="<?= clean_input($item['title']) ?>">
            <div class="card-body">
              <h5 class="card-title"><?= clean_input($item['title']) ?></h5>
              <p class="text-muted small mb-1">Client: <?= clean_input($item['client_name']) ?></p>
              <p class="card-text"><?= clean_input($item['description']) ?></p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (empty($items)): ?>
        <p class="text-center text-muted">No portfolio items added yet.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
