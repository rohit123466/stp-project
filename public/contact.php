<?php
/**
 * contact.php
 * Public contact form. Validates input server-side (never trust the
 * client), then inserts a new row into `leads` with status 'New' using
 * a prepared statement.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$errors = [];
$old = ['name' => '', 'email' => '', 'phone' => '', 'message' => ''];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['name']    = trim($_POST['name'] ?? '');
    $old['email']   = trim($_POST['email'] ?? '');
    $old['phone']   = trim($_POST['phone'] ?? '');
    $old['message'] = trim($_POST['message'] ?? '');

    // --- Server-side validation (authoritative; JS validation is just UX) ---
    if (mb_strlen($old['name']) < 2) {
        $errors['name'] = 'Please enter your full name.';
    }
    if (!is_valid_email($old['email'])) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    if ($old['phone'] !== '' && !preg_match('/^[0-9+\-\s()]{7,20}$/', $old['phone'])) {
        $errors['phone'] = 'Please enter a valid phone number.';
    }
    if (mb_strlen($old['message']) < 10) {
        $errors['message'] = 'Message should be at least 10 characters long.';
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO leads (name, email, phone, message, status) VALUES (?, ?, ?, ?, 'New')");
        mysqli_stmt_bind_param($stmt, 'ssss', $old['name'], $old['email'], $old['phone'], $old['message']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Redirect so refreshing the page doesn't resubmit the form
        $_SESSION['contact_success'] = true;
        header('Location: contact.php?sent=1');
        exit;
    }
}

if (isset($_GET['sent']) && !empty($_SESSION['contact_success'])) {
    $success = true;
    unset($_SESSION['contact_success']);
}

$pageTitle = 'Contact Us - GroviX Digital';
$metaDescription = 'Get in touch with GroviX Digital for a free digital marketing consultation.';

require_once __DIR__ . '/../includes/header.php';
?>

<section class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <h1 class="fw-bold">Contact Us</h1>
      <p class="text-muted">Tell us about your business and we'll get back to you shortly.</p>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-7">

        <?php if ($success): ?>
          <div class="alert alert-success">Thanks for reaching out! We'll be in touch soon.</div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger">Please fix the errors below and try again.</div>
        <?php endif; ?>

        <form id="contactForm" method="POST" action="contact.php" novalidate>
          <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= clean_input($old['name']) ?>">
            <div class="invalid-feedback"><?= clean_input($errors['name'] ?? '') ?></div>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= clean_input($old['email']) ?>">
            <div class="invalid-feedback"><?= clean_input($errors['email'] ?? '') ?></div>
          </div>

          <div class="mb-3">
            <label for="phone" class="form-label">Phone Number <span class="text-muted small">(optional)</span></label>
            <input type="text" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" id="phone" name="phone" value="<?= clean_input($old['phone']) ?>">
            <div class="invalid-feedback"><?= clean_input($errors['phone'] ?? '') ?></div>
          </div>

          <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control <?= isset($errors['message']) ? 'is-invalid' : '' ?>" id="message" name="message" rows="5"><?= clean_input($old['message']) ?></textarea>
            <div class="invalid-feedback"><?= clean_input($errors['message'] ?? '') ?></div>
          </div>

          <button type="submit" class="btn btn-warning w-100">Send Message</button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
