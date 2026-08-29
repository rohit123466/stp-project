<?php
/**
 * manage_testimonials.php
 * Full CRUD for the `testimonials` table, including image upload and rating.
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$errors = [];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save') {
    $id = (int) ($_POST['id'] ?? 0);
    $clientName = trim($_POST['client_name'] ?? '');
    $messageText = trim($_POST['message'] ?? '');
    $rating = (int) ($_POST['rating'] ?? 5);

    if ($clientName === '') $errors[] = 'Client name is required.';
    if ($messageText === '') $errors[] = 'Testimonial message is required.';
    if ($rating < 1 || $rating > 5) $errors[] = 'Rating must be between 1 and 5.';

    $upload = handle_image_upload('image');
    if (!$upload['ok']) $errors[] = $upload['error'];

    if (empty($errors)) {
        if ($id > 0) {
            if ($upload['filename']) {
                $stmt = mysqli_prepare($conn, "UPDATE testimonials SET client_name=?, message=?, rating=?, image=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, 'ssisi', $clientName, $messageText, $rating, $upload['filename'], $id);
            } else {
                $stmt = mysqli_prepare($conn, "UPDATE testimonials SET client_name=?, message=?, rating=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, 'ssii', $clientName, $messageText, $rating, $id);
            }
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $message = 'Testimonial updated successfully.';
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO testimonials (client_name, message, rating, image) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'ssis', $clientName, $messageText, $rating, $upload['filename']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $message = 'Testimonial added successfully.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = mysqli_prepare($conn, "DELETE FROM testimonials WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $message = 'Testimonial deleted.';
}

$editRow = null;
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM testimonials WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $editId);
    mysqli_stmt_execute($stmt);
    $editRow = mysqli_stmt_get_result($stmt)->fetch_assoc();
    mysqli_stmt_close($stmt);
}

$testimonials = [];
$result = mysqli_query($conn, "SELECT * FROM testimonials ORDER BY created_at DESC");
while ($row = mysqli_fetch_assoc($result)) {
    $testimonials[] = $row;
}

$pageTitle = 'Manage Testimonials';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if ($errors): ?><div class="alert alert-danger"><?php foreach ($errors as $e) echo htmlspecialchars($e) . '<br>'; ?></div><?php endif; ?>

<div class="card p-4 mb-4">
  <h5 class="fw-bold mb-3"><?= $editRow ? 'Edit Testimonial' : 'Add New Testimonial' ?></h5>
  <form method="POST" action="manage_testimonials.php" enctype="multipart/form-data">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= $editRow['id'] ?? 0 ?>">

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Client Name</label>
        <input type="text" name="client_name" class="form-control" required value="<?= htmlspecialchars($editRow['client_name'] ?? '') ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Rating</label>
        <select name="rating" class="form-select">
          <?php for ($i = 5; $i >= 1; $i--): ?>
            <option value="<?= $i ?>" <?= (($editRow['rating'] ?? 5) == $i) ? 'selected' : '' ?>><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Photo <span class="text-muted small">(optional)</span></label>
        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp" data-preview="#previewImg">
      </div>
      <div class="col-12">
        <img id="previewImg" src="<?= image_url($editRow['image'] ?? null) ?>" class="rounded-circle" style="width:70px;height:70px;object-fit:cover;">
      </div>
      <div class="col-12">
        <label class="form-label">Message</label>
        <textarea name="message" class="form-control" rows="3" required><?= htmlspecialchars($editRow['message'] ?? '') ?></textarea>
      </div>
    </div>

    <div class="mt-3">
      <button type="submit" class="btn btn-warning"><?= $editRow ? 'Update Testimonial' : 'Add Testimonial' ?></button>
      <?php if ($editRow): ?><a href="manage_testimonials.php" class="btn btn-outline-secondary">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card p-3">
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr><th>Photo</th><th>Client</th><th>Message</th><th>Rating</th><th></th></tr>
      </thead>
      <tbody>
        <?php foreach ($testimonials as $t): ?>
          <tr>
            <td><img src="<?= image_url($t['image']) ?>" style="width:45px;height:45px;object-fit:cover;" class="rounded-circle"></td>
            <td><?= htmlspecialchars($t['client_name']) ?></td>
            <td class="small text-muted"><?= htmlspecialchars(mb_strimwidth($t['message'], 0, 60, '...')) ?></td>
            <td><span class="rating-stars"><?= str_repeat('&#9733;', (int)$t['rating']) ?></span></td>
            <td class="text-nowrap">
              <a href="manage_testimonials.php?edit=<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
              <form method="POST" action="manage_testimonials.php" class="d-inline" onsubmit="return confirm('Delete this testimonial?');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($testimonials)): ?>
          <tr><td colspan="5" class="text-center text-muted">No testimonials yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
