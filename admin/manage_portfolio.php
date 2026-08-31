<?php
/**
 * manage_portfolio.php
 * Full CRUD for the `portfolio` table, including image upload.
 * Same create/edit/delete pattern as manage_services.php.
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$errors = [];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save') {
    $id = (int) ($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $clientName = trim($_POST['client_name'] ?? '');
    $projectUrl = trim($_POST['project_url'] ?? '');

    if ($title === '') $errors[] = 'Title is required.';
    if ($description === '') $errors[] = 'Description is required.';
    if ($clientName === '') $errors[] = 'Client name is required.';
    if ($projectUrl !== '' && filter_var($projectUrl, FILTER_VALIDATE_URL) === false) $errors[] = 'Project URL must be a valid URL.';

    $upload = handle_image_upload('image');
    if (!$upload['ok']) $errors[] = $upload['error'];

    if (empty($errors)) {
        $projectUrlValue = $projectUrl !== '' ? $projectUrl : null;
        if ($id > 0) {
            if ($upload['filename']) {
                $stmt = mysqli_prepare($conn, "UPDATE portfolio SET title=?, description=?, image=?, client_name=?, project_url=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, 'sssssi', $title, $description, $upload['filename'], $clientName, $projectUrlValue, $id);
            } else {
                $stmt = mysqli_prepare($conn, "UPDATE portfolio SET title=?, description=?, client_name=?, project_url=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, 'ssssi', $title, $description, $clientName, $projectUrlValue, $id);
            }
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $message = 'Portfolio item updated successfully.';
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO portfolio (title, description, image, client_name, project_url) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'sssss', $title, $description, $upload['filename'], $clientName, $projectUrlValue);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $message = 'Portfolio item added successfully.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = mysqli_prepare($conn, "DELETE FROM portfolio WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $message = 'Portfolio item deleted.';
}

$editRow = null;
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM portfolio WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $editId);
    mysqli_stmt_execute($stmt);
    $editRow = mysqli_stmt_get_result($stmt)->fetch_assoc();
    mysqli_stmt_close($stmt);
}

$items = [];
$result = mysqli_query($conn, "SELECT * FROM portfolio ORDER BY created_at DESC");
while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
}

$pageTitle = 'Manage Portfolio';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if ($errors): ?><div class="alert alert-danger"><?php foreach ($errors as $e) echo htmlspecialchars($e) . '<br>'; ?></div><?php endif; ?>

<div class="card p-4 mb-4">
  <h5 class="fw-bold mb-3"><?= $editRow ? 'Edit Portfolio Item' : 'Add New Portfolio Item' ?></h5>
  <form method="POST" action="manage_portfolio.php" enctype="multipart/form-data">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= $editRow['id'] ?? 0 ?>">

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($editRow['title'] ?? '') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Client Name</label>
        <input type="text" name="client_name" class="form-control" required value="<?= htmlspecialchars($editRow['client_name'] ?? '') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Project URL <span class="text-muted small">(optional)</span></label>
        <input type="url" name="project_url" class="form-control" placeholder="https://example.com" value="<?= htmlspecialchars($editRow['project_url'] ?? '') ?>">
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
    </div>

    <div class="mt-3">
      <button type="submit" class="btn btn-warning"><?= $editRow ? 'Update Item' : 'Add Item' ?></button>
      <?php if ($editRow): ?><a href="manage_portfolio.php" class="btn btn-outline-secondary">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card p-3">
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr><th>Image</th><th>Title</th><th>Client</th><th>Added</th><th></th></tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item): ?>
          <tr>
            <td><img src="<?= image_url($item['image']) ?>" style="width:60px;height:45px;object-fit:cover;" class="rounded"></td>
            <td><?= htmlspecialchars($item['title']) ?></td>
            <td><?= htmlspecialchars($item['client_name']) ?><?php if (!empty($item['project_url'])): ?><br><a href="<?= htmlspecialchars($item['project_url']) ?>" target="_blank" rel="noopener noreferrer" class="small">visit &rarr;</a><?php endif; ?></td>
            <td class="small text-muted"><?= htmlspecialchars($item['created_at']) ?></td>
            <td class="text-nowrap">
              <a href="manage_portfolio.php?edit=<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
              <form method="POST" action="manage_portfolio.php" class="d-inline" onsubmit="return confirm('Delete this item?');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($items)): ?>
          <tr><td colspan="5" class="text-center text-muted">No portfolio items yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
