<?php
/**
 * change_password.php
 * Lets the logged-in admin change their own password.
 * Requires the current password to be entered correctly first.
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$errors = [];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT password FROM admins WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $_SESSION['admin_id']);
    mysqli_stmt_execute($stmt);
    $admin = mysqli_stmt_get_result($stmt)->fetch_assoc();
    mysqli_stmt_close($stmt);

    if (!$admin || !password_verify($currentPassword, $admin['password'])) {
        $errors[] = 'Current password is incorrect.';
    }
    if (strlen($newPassword) < 6) {
        $errors[] = 'New password must be at least 6 characters long.';
    }
    if ($newPassword !== $confirmPassword) {
        $errors[] = 'New password and confirmation do not match.';
    }

    if (empty($errors)) {
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "UPDATE admins SET password = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'si', $newHash, $_SESSION['admin_id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $message = 'Password changed successfully.';
    }
}

$pageTitle = 'Change Password';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card p-4">

      <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
      <?php if ($errors): ?>
        <div class="alert alert-danger"><?php foreach ($errors as $e) echo htmlspecialchars($e) . '<br>'; ?></div>
      <?php endif; ?>

      <form method="POST" action="change_password.php">
        <div class="mb-3">
          <label class="form-label">Current Password</label>
          <input type="password" name="current_password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">New Password</label>
          <input type="password" name="new_password" class="form-control" required minlength="6">
        </div>
        <div class="mb-3">
          <label class="form-label">Confirm New Password</label>
          <input type="password" name="confirm_password" class="form-control" required minlength="6">
        </div>
        <button type="submit" class="btn btn-warning">Update Password</button>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
