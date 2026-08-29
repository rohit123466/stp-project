<?php
/**
 * about.php - static About Us page
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'About Us - OpportuneX Digital';
$metaDescription = 'Learn more about OpportuneX Digital, our team, and our mission to help businesses grow online.';

require_once __DIR__ . '/../includes/header.php';
?>

<section class="py-5">
  <div class="container">
    <h1 class="fw-bold mb-4">About OpportuneX Digital</h1>
    <div class="row align-items-center g-4">
      <div class="col-lg-6">
        <p>
          OpportuneX Digital is a full-service digital marketing agency dedicated to helping
          small and mid-sized businesses grow their online presence. Since our founding, we have
          worked with clients across retail, hospitality, fitness, and e-commerce to deliver
          measurable growth through SEO, social media, web design, and paid advertising.
        </p>
        <p>
          Our approach is simple: understand your business goals, build a strategy around real
          data, and execute with transparency. No jargon, no inflated reports -- just results
          you can see in your traffic, leads, and revenue.
        </p>
      </div>
      <div class="col-lg-6">
        <div class="card p-4">
          <h5 class="fw-bold">Our Mission</h5>
          <p class="text-muted">To make world-class digital marketing accessible to growing businesses.</p>
          <h5 class="fw-bold mt-3">Our Values</h5>
          <ul class="text-muted mb-0">
            <li>Data-driven decision making</li>
            <li>Transparent reporting</li>
            <li>Long-term client partnerships</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
