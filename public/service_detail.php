<?php
/**
 * service_detail.php - full page for a single service, linked from the
 * service cards on index.php and services.php.
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/seo_audit.php';
require_once __DIR__ . '/../includes/social_caption_generator.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$service = null;

if ($id) {
    $stmt = mysqli_prepare($conn, "SELECT id, title, description, image, meta_title, meta_description FROM services WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $service = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

$pageTitle = $service ? $service['meta_title'] : 'Service Not Found - OpportuneX Digital';
$metaDescription = $service ? $service['meta_description'] : 'The requested service could not be found.';

// The SEO service gets an instant, real audit tool right on its page
// instead of only a "contact us" call to action.
$isSeoService = $service && (
    str_contains(strtolower($service['title']), 'seo') ||
    str_contains(strtolower($service['title']), 'search engine')
);

$auditResult = null;
if ($isSeoService && isset($_GET['audit_url']) && trim($_GET['audit_url']) !== '') {
    if (!seo_audit_rate_limit_ok()) {
        $auditResult = ['ok' => false, 'error' => 'Too many audits from this browser. Please try again in a bit.'];
    } else {
        $auditResult = run_seo_audit($_GET['audit_url']);
    }
}

// The Social Media Marketing service gets an instant caption + hashtag
// generator right on its page instead of only a "contact us" call to action.
$isSocialService = $service && (
    str_contains(strtolower($service['title']), 'social media') ||
    str_contains(strtolower($service['title']), 'smm')
);

$socialResult = null;
if ($isSocialService && isset($_GET['topic']) && trim($_GET['topic']) !== '') {
    $topic = mb_substr(trim($_GET['topic']), 0, 100);
    $platform = $_GET['platform'] ?? 'instagram';
    $tone = $_GET['tone'] ?? 'professional';
    $socialResult = generate_social_content($topic, $platform, $tone);
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="py-5">
  <div class="container">
    <?php if ($service): ?>
      <div class="row justify-content-center align-items-start g-5">
        <div class="col-lg-5">
          <img src="<?= service_hero_image_url($service['image'], $service['title']) ?>" class="img-fluid rounded" alt="<?= clean_input($service['title']) ?>">
        </div>
        <div class="col-lg-7">
          <h1 class="fw-bold mb-3"><?= clean_input($service['title']) ?></h1>
          <p class="lead text-muted"><?= nl2br(clean_input($service['description'])) ?></p>
          <a href="contact.php" class="btn btn-warning btn-lg mt-3">Get a Free Consultation</a>
          <a href="services.php" class="btn btn-outline-dark btn-lg mt-3">Back to All Services</a>
        </div>
      </div>

      <?php if ($isSeoService): ?>
      <div class="row justify-content-center mt-5">
        <div class="col-lg-10">
          <div class="card shadow-sm border-0">
            <div class="card-body p-4">
              <h2 class="fw-bold h4 mb-1">Free Instant SEO Audit</h2>
              <p class="text-muted">Enter your website URL and get a real, on-page SEO report right now — no waiting for a consultation.</p>
              <form method="get" action="" class="row g-2 mb-4">
                <input type="hidden" name="id" value="<?= (int) $service['id'] ?>">
                <div class="col-sm-9">
                  <input type="text" name="audit_url" class="form-control" placeholder="e.g. https://example.com"
                         value="<?= isset($_GET['audit_url']) ? clean_input($_GET['audit_url']) : '' ?>" required>
                </div>
                <div class="col-sm-3 d-grid">
                  <button type="submit" class="btn btn-warning fw-semibold">Run Free Audit</button>
                </div>
              </form>

              <?php if ($auditResult && !$auditResult['ok']): ?>
                <div class="alert alert-danger mb-0"><?= clean_input($auditResult['error']) ?></div>
              <?php elseif ($auditResult && $auditResult['ok']): ?>
                <?php
                  $score = $auditResult['score'];
                  $scoreClass = $score >= 80 ? 'success' : ($score >= 50 ? 'warning' : 'danger');
                ?>
                <div class="d-flex align-items-center gap-3 mb-3">
                  <div class="display-6 fw-bold text-<?= $scoreClass ?>"><?= $score ?>/100</div>
                  <div class="flex-grow-1">
                    <div class="progress" style="height: 10px;">
                      <div class="progress-bar bg-<?= $scoreClass ?>" style="width: <?= $score ?>%"></div>
                    </div>
                    <small class="text-muted">Audit for <?= clean_input($auditResult['url']) ?></small>
                  </div>
                </div>
                <ul class="list-group list-group-flush mb-3">
                  <?php foreach ($auditResult['checks'] as $check):
                    $badge = ['pass' => 'success', 'warn' => 'warning', 'fail' => 'danger'][$check['level']];
                    $icon = ['pass' => '✓', 'warn' => '!', 'fail' => '✕'][$check['level']];
                  ?>
                  <li class="list-group-item d-flex align-items-start gap-2 px-0">
                    <span class="badge bg-<?= $badge ?> mt-1"><?= $icon ?></span>
                    <div>
                      <div class="fw-semibold"><?= clean_input($check['label']) ?></div>
                      <div class="text-muted small"><?= clean_input($check['detail']) ?></div>
                    </div>
                  </li>
                  <?php endforeach; ?>
                </ul>
                <p class="mb-2">Want these issues actually fixed, not just flagged?</p>
                <a href="contact.php" class="btn btn-warning">Get a Free Consultation</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($isSocialService): ?>
      <div class="row justify-content-center mt-5">
        <div class="col-lg-10">
          <div class="card shadow-sm border-0">
            <div class="card-body p-4">
              <h2 class="fw-bold h4 mb-1">Free Caption &amp; Hashtag Generator</h2>
              <p class="text-muted">Tell us your topic and get ready-to-post captions and hashtags right now.</p>
              <form method="get" action="" class="row g-2 mb-4">
                <input type="hidden" name="id" value="<?= (int) $service['id'] ?>">
                <div class="col-sm-5">
                  <input type="text" name="topic" class="form-control" placeholder="e.g. our new summer sale"
                         value="<?= isset($_GET['topic']) ? clean_input($_GET['topic']) : '' ?>" maxlength="100" required>
                </div>
                <div class="col-sm-3">
                  <select name="platform" class="form-select">
                    <?php foreach (SOCIAL_PLATFORMS as $p): ?>
                      <option value="<?= $p ?>" <?= (($_GET['platform'] ?? 'instagram') === $p) ? 'selected' : '' ?>><?= ucfirst($p) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-sm-2">
                  <select name="tone" class="form-select">
                    <?php foreach (SOCIAL_TONES as $t): ?>
                      <option value="<?= $t ?>" <?= (($_GET['tone'] ?? 'professional') === $t) ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-sm-2 d-grid">
                  <button type="submit" class="btn btn-warning fw-semibold">Generate</button>
                </div>
              </form>

              <?php if ($socialResult): ?>
                <h3 class="h6 fw-bold">Caption ideas</h3>
                <ul class="list-group list-group-flush mb-3">
                  <?php foreach ($socialResult['captions'] as $caption): ?>
                  <li class="list-group-item px-0"><?= clean_input($caption) ?></li>
                  <?php endforeach; ?>
                </ul>
                <h3 class="h6 fw-bold">Suggested hashtags</h3>
                <p class="mb-3">
                  <?php foreach ($socialResult['hashtags'] as $tag): ?>
                    <span class="badge bg-light text-dark border me-1 mb-1"><?= clean_input($tag) ?></span>
                  <?php endforeach; ?>
                </p>
                <p class="mb-2">Want a full content calendar built around this, not just one post?</p>
                <a href="contact.php" class="btn btn-warning">Get a Free Consultation</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>
    <?php else: ?>
      <div class="text-center">
        <h1 class="fw-bold">Service Not Found</h1>
        <p class="text-muted">The service you're looking for doesn't exist or may have been removed.</p>
        <a href="services.php" class="btn btn-outline-dark mt-3">View All Services</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
