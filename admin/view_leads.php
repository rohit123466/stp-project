<?php
/**
 * view_leads.php
 * Lists all contact-form leads, with search (by name) and status filter.
 * Each row has a status dropdown that updates the lead's pipeline stage
 * in place via a small POST form.
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$validStatuses = ['New', 'Contacted', 'Interested', 'Converted', 'Lost'];
$message = '';

// --- Update a lead's status ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_status') {
    $id = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';

    if (in_array($status, $validStatuses, true)) {
        $stmt = mysqli_prepare($conn, "UPDATE leads SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'si', $status, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $message = 'Lead status updated.';
    }
}

// --- Search / filter ---
// Handled as explicit cases (rather than dynamically building the bind
// list) so every query stays a plain, easy-to-read prepared statement.
$search = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? '';
if (!in_array($statusFilter, $validStatuses, true)) {
    $statusFilter = '';
}
$searchParam = '%' . $search . '%';

if ($search !== '' && $statusFilter !== '') {
    $stmt = mysqli_prepare($conn, "SELECT * FROM leads WHERE name LIKE ? AND status = ? ORDER BY created_at DESC");
    mysqli_stmt_bind_param($stmt, 'ss', $searchParam, $statusFilter);
} elseif ($search !== '') {
    $stmt = mysqli_prepare($conn, "SELECT * FROM leads WHERE name LIKE ? ORDER BY created_at DESC");
    mysqli_stmt_bind_param($stmt, 's', $searchParam);
} elseif ($statusFilter !== '') {
    $stmt = mysqli_prepare($conn, "SELECT * FROM leads WHERE status = ? ORDER BY created_at DESC");
    mysqli_stmt_bind_param($stmt, 's', $statusFilter);
} else {
    $stmt = mysqli_prepare($conn, "SELECT * FROM leads ORDER BY created_at DESC");
}

mysqli_stmt_execute($stmt);
$leads = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

$pageTitle = 'Leads';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>

<div class="card p-3 mb-3">
  <form method="GET" action="view_leads.php" class="row g-2 align-items-end">
    <div class="col-md-5">
      <label class="form-label small">Search by name</label>
      <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search) ?>" placeholder="e.g. Rahul">
    </div>
    <div class="col-md-4">
      <label class="form-label small">Filter by status</label>
      <select name="status" class="form-select">
        <option value="">All Statuses</option>
        <?php foreach ($validStatuses as $s): ?>
          <option value="<?= $s ?>" <?= $statusFilter === $s ? 'selected' : '' ?>><?= $s ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <button type="submit" class="btn btn-warning w-100">Apply</button>
    </div>
  </form>
</div>

<div class="card p-3">
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>Name</th><th>Email</th><th>Phone</th><th>Message</th><th>Received</th><th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($leads as $lead): ?>
          <tr>
            <td><?= htmlspecialchars($lead['name']) ?></td>
            <td><?= htmlspecialchars($lead['email']) ?></td>
            <td><?= htmlspecialchars($lead['phone'] ?: '-') ?></td>
            <td class="small text-muted"><?= htmlspecialchars(mb_strimwidth($lead['message'], 0, 50, '...')) ?></td>
            <td class="small text-muted"><?= htmlspecialchars($lead['created_at']) ?></td>
            <td>
              <span class="badge <?= status_badge_class($lead['status']) ?> mb-1 d-inline-block"><?= htmlspecialchars($lead['status']) ?></span>
              <form method="POST" action="view_leads.php" class="d-flex gap-1">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="id" value="<?= $lead['id'] ?>">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                  <?php foreach ($validStatuses as $s): ?>
                    <option value="<?= $s ?>" <?= $lead['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                  <?php endforeach; ?>
                </select>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($leads)): ?>
          <tr><td colspan="6" class="text-center text-muted">No leads found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
