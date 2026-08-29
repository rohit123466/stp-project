<?php
/**
 * dashboard.php
 * Admin landing page: quick counts of services, portfolio items,
 * testimonials, and a breakdown of leads by pipeline status.
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

function count_rows($conn, $table) {
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM {$table}");
    return (int) mysqli_fetch_assoc($result)['total'];
}

$totalServices = count_rows($conn, 'services');
$totalPortfolio = count_rows($conn, 'portfolio');
$totalTestimonials = count_rows($conn, 'testimonials');

// Lead counts grouped by status
$leadStatuses = ['New', 'Contacted', 'Interested', 'Converted', 'Lost'];
$leadCounts = array_fill_keys($leadStatuses, 0);

$result = mysqli_query($conn, "SELECT status, COUNT(*) AS total FROM leads GROUP BY status");
while ($row = mysqli_fetch_assoc($result)) {
    $leadCounts[$row['status']] = (int) $row['total'];
}
$totalLeads = array_sum($leadCounts);

$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card stat-card p-3">
      <div class="text-muted small">Total Services</div>
      <div class="fs-2 fw-bold"><?= $totalServices ?></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card stat-card p-3">
      <div class="text-muted small">Portfolio Items</div>
      <div class="fs-2 fw-bold"><?= $totalPortfolio ?></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card stat-card p-3">
      <div class="text-muted small">Testimonials</div>
      <div class="fs-2 fw-bold"><?= $totalTestimonials ?></div>
    </div>
  </div>
</div>

<h5 class="fw-bold mb-3">Leads by Status (<?= $totalLeads ?> total)</h5>
<div class="row g-3">
  <?php foreach ($leadStatuses as $status): ?>
    <div class="col-6 col-md-3 col-lg-2">
      <div class="card stat-card p-3 text-center">
        <div class="small mb-1"><?= $status ?></div>
        <span class="badge <?= status_badge_class($status) ?> fs-6"><?= $leadCounts[$status] ?></span>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="mt-4">
  <a href="view_leads.php" class="btn btn-outline-dark btn-sm">View All Leads &rarr;</a>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
