<?php
/**
 * manage_services.php
 * Full CRUD for the `services` table, including image upload.
 * URL patterns:
 *   manage_services.php              -> list + add form
 *   manage_services.php?edit=ID      -> list + edit form pre-filled
 *   POST action=save                 -> insert or update
 *   POST action=delete                -> delete one row
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$errors = [];
$message = '';

// --- Handle form submission (create / update) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save') {
    $id = (int) ($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $metaTitle = trim($_POST['meta_title'] ?? '');
    $metaDescription = trim($_POST['meta_description'] ?? '');

    if ($title === '') $errors[] = 'Title is required.';
    if ($description === '') $errors[] = 'Description is required.';

    $upload = handle_image_upload('image');
    if (!$upload['ok']) $errors[] = $upload['error'];

    if (empty($errors)) {
        if ($id > 0) {
            // Update. Only overwrite image if a new one was uploaded.
            if ($upload['filename']) {
                $stmt = mysqli_prepare($conn, "UPDATE services SET title=?, description=?, image=?, meta_title=?, meta_description=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, 'sssssi', $title, $description, $upload['filename'], $metaTitle, $metaDescription, $id);
            } else {
                $stmt = mysqli_prepare($conn, "UPDATE services SET title=?, description=?, meta_title=?, meta_description=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, 'ssssi', $title, $description, $metaTitle, $metaDescription, $id);
            }
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $message = 'Service updated successfully.';
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO services (title, description, image, meta_title, meta_description) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'sssss', $title, $description, $upload['filename'], $metaTitle, $metaDescription);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $message = 'Service added successfully.';
        }
    }
}

// --- Handle delete ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = mysqli_prepare($conn, "DELETE FROM services WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $message = 'Service deleted.';
}

// --- Load record for editing, if requested ---
$editRow = null;
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM services WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $editId);
    mysqli_stmt_execute($stmt);
    $editRow = mysqli_stmt_get_result($stmt)->fetch_assoc();
    mysqli_stmt_close($stmt);
}

// --- Fetch all services for the table ---
$services = [];
$result = mysqli_query($conn, "SELECT * FROM services ORDER BY created_at DESC");
while ($row = mysqli_fetch_assoc($result)) {
    $services[] = $row;
}

$pageTitle = 'Manage Services';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if ($errors): ?><div class="alert alert-danger"><?php foreach ($errors as $e) echo htmlspecialchars($e) . '<br>'; ?></div><?php endif; ?>

<div class="card p-4 mb-4">
  <h5 class="fw-bold mb-3"><?= $editRow ? 'Edit Service' : 'Add New Service' ?></h5>
  <form method="POST" action="manage_services.php" enctype="multipart/form-data">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= $editRow['id'] ?? 0 ?>">

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($editRow['title'] ?? '') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Image <span class="text-muted small">(JPG/PNG/WEBP, max 2MB)</span></label>
        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp" data-preview="#previewImg">
        <img id="previewImg" src="<?= image_url($editRow['image'] ?? null) ?>" class="mt-2 rounded" style="max-height:100px;">
      </div>
      <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($editRow['description'] ?? '') ?></textarea>
      </div>
      <div class="col-md-6">
        <label class="form-label">Meta Title <span class="text-muted small">(SEO)</span></label>
        <input type="text" name="meta_title" class="form-control" value="<?= htmlspecialchars($editRow['meta_title'] ?? '') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Meta Description <span class="text-muted small">(SEO)</span></label>
        <input type="text" name="meta_description" class="form-control" value="<?= htmlspecialchars($editRow['meta_description'] ?? '') ?>">
      </div>
    </div>

    <div class="mt-3">
      <button type="submit" class="btn btn-warning"><?= $editRow ? 'Update Service' : 'Add Service' ?></button>
      <?php if ($editRow): ?><a href="manage_services.php" class="btn btn-outline-secondary">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card p-3">
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr><th>Image</th><th>Title</th><th>Description</th><th>Added</th><th></th></tr>
      </thead>
      <tbody>
        <?php foreach ($services as $s): ?>
          <tr>
            <td><img src="<?= image_url($s['image']) ?>" style="width:60px;height:45px;object-fit:cover;" class="rounded"></td>
            <td><?= htmlspecialchars($s['title']) ?></td>
            <td class="small text-muted"><?= htmlspecialchars(mb_strimwidth($s['description'], 0, 60, '...')) ?></td>
            <td class="small text-muted"><?= htmlspecialchars($s['created_at']) ?></td>
            <td class="text-nowrap">
              <a href="manage_services.php?edit=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
              <form method="POST" action="manage_services.php" class="d-inline" onsubmit="return confirm('Delete this service?');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($services)): ?>
          <tr><td colspan="5" class="text-center text-muted">No services yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
