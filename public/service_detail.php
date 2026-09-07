<?php
/**
 * service_detail.php - full page for a single service, linked from the
 * service cards on index.php and services.php.
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/seo_audit.php';
require_once __DIR__ . '/../includes/social_caption_generator.php';
require_once __DIR__ . '/../includes/web_health_check.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$service = null;

if ($id) {
    $stmt = mysqli_prepare($conn, "SELECT id, title, description, image, meta_title, meta_description FROM services WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $service = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

$pageTitle = $service ? $service['meta_title'] : 'Service Not Found - Grovix Digital';
$metaDescription = $service ? $service['meta_description'] : 'The requested service could not be found.';

// The SEO service gets an instant, real audit tool right on its page
// instead of only a "contact us" call to action.
$isSeoService = $service && (
    str_contains(strtolower($service['title']), 'seo') ||
    str_contains(strtolower($service['title']), 'search engine')
);

$auditResult = null;
if ($isSeoService && isset($_GET['audit_url']) && trim($_GET['audit_url']) !== '') {
    if (!url_fetch_rate_limit_ok('seo_audit')) {
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

// The Website Design & Development service gets an instant health check
// (real UX/mobile-friendliness signals) plus a color contrast checker,
// instead of only a "contact us" call to action.
$isWebDesignService = $service && (
    str_contains(strtolower($service['title']), 'web design') ||
    str_contains(strtolower($service['title']), 'web development') ||
    str_contains(strtolower($service['title']), 'website')
);

$healthResult = null;
if ($isWebDesignService && isset($_GET['health_url']) && trim($_GET['health_url']) !== '') {
    if (!url_fetch_rate_limit_ok('web_health')) {
        $healthResult = ['ok' => false, 'error' => 'Too many checks from this browser. Please try again in a bit.'];
    } else {
        $healthResult = run_web_health_check($_GET['health_url']);
    }
}

$contrastResult = null;
if ($isWebDesignService && isset($_GET['color1']) && isset($_GET['color2']) && trim($_GET['color1']) !== '' && trim($_GET['color2']) !== '') {
    $contrastResult = check_color_contrast($_GET['color1'], $_GET['color2']);
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

      <?php if ($isWebDesignService): ?>
      <div class="row justify-content-center mt-5">
        <div class="col-lg-10">
          <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
              <h2 class="fw-bold h4 mb-1">Free Website Health Check</h2>
              <p class="text-muted">Enter your website URL and get a real report on mobile-friendliness, speed, and page structure.</p>
              <form method="get" action="" class="row g-2 mb-4">
                <input type="hidden" name="id" value="<?= (int) $service['id'] ?>">
                <div class="col-sm-9">
                  <input type="text" name="health_url" class="form-control" placeholder="e.g. https://example.com"
                         value="<?= isset($_GET['health_url']) ? clean_input($_GET['health_url']) : '' ?>" required>
                </div>
                <div class="col-sm-3 d-grid">
                  <button type="submit" class="btn btn-warning fw-semibold">Run Health Check</button>
                </div>
              </form>

              <?php if ($healthResult && !$healthResult['ok']): ?>
                <div class="alert alert-danger mb-0"><?= clean_input($healthResult['error']) ?></div>
              <?php elseif ($healthResult && $healthResult['ok']): ?>
                <?php
                  $hScore = $healthResult['score'];
                  $hScoreClass = $hScore >= 80 ? 'success' : ($hScore >= 50 ? 'warning' : 'danger');
                ?>
                <div class="d-flex align-items-center gap-3 mb-3">
                  <div class="display-6 fw-bold text-<?= $hScoreClass ?>"><?= $hScore ?>/100</div>
                  <div class="flex-grow-1">
                    <div class="progress" style="height: 10px;">
                      <div class="progress-bar bg-<?= $hScoreClass ?>" style="width: <?= $hScore ?>%"></div>
                    </div>
                    <small class="text-muted">Health check for <?= clean_input($healthResult['url']) ?></small>
                  </div>
                </div>
                <ul class="list-group list-group-flush mb-3">
                  <?php foreach ($healthResult['checks'] as $check):
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
                <p class="mb-2">Want a site that scores 90+ on every one of these?</p>
                <a href="contact.php" class="btn btn-warning">Get a Free Consultation</a>
              <?php endif; ?>
            </div>
          </div>

          <div class="card shadow-sm border-0">
            <div class="card-body p-4">
              <h2 class="fw-bold h4 mb-1">Color Contrast Checker</h2>
              <p class="text-muted">Check whether your text and background colors meet WCAG accessibility standards.</p>
              <form method="get" action="" class="row g-2 align-items-end mb-4">
                <input type="hidden" name="id" value="<?= (int) $service['id'] ?>">
                <div class="col-sm-4">
                  <label class="form-label small mb-1">Text color</label>
                  <div class="input-group">
                    <input type="color" class="form-control form-control-color" name="color1_picker"
                           value="<?= isset($_GET['color1']) && parse_hex_color($_GET['color1']) ? clean_input($_GET['color1']) : '#333333' ?>"
                           oninput="this.form.color1.value=this.value">
                    <input type="text" name="color1" class="form-control" placeholder="#333333"
                           value="<?= isset($_GET['color1']) ? clean_input($_GET['color1']) : '#333333' ?>">
                  </div>
                </div>
                <div class="col-sm-4">
                  <label class="form-label small mb-1">Background color</label>
                  <div class="input-group">
                    <input type="color" class="form-control form-control-color" name="color2_picker"
                           value="<?= isset($_GET['color2']) && parse_hex_color($_GET['color2']) ? clean_input($_GET['color2']) : '#ffffff' ?>"
                           oninput="this.form.color2.value=this.value">
                    <input type="text" name="color2" class="form-control" placeholder="#ffffff"
                           value="<?= isset($_GET['color2']) ? clean_input($_GET['color2']) : '#ffffff' ?>">
                  </div>
                </div>
                <div class="col-sm-4 d-grid">
                  <button type="submit" class="btn btn-warning fw-semibold">Check Contrast</button>
                </div>
              </form>

              <?php if ($contrastResult && !$contrastResult['ok']): ?>
                <div class="alert alert-danger mb-0"><?= clean_input($contrastResult['error']) ?></div>
              <?php elseif ($contrastResult && $contrastResult['ok']): ?>
                <div class="d-flex align-items-center gap-4 mb-3 flex-wrap">
                  <div class="p-4 rounded border d-flex align-items-center justify-content-center"
                       style="background-color: <?= clean_input($_GET['color2']) ?>; color: <?= clean_input($_GET['color1']) ?>; min-width: 180px;">
                    Sample Text
                  </div>
                  <div class="display-6 fw-bold"><?= round($contrastResult['ratio'], 2) ?>:1</div>
                </div>
                <ul class="list-group list-group-flush mb-3">
                  <?php
                    $rows = [
                      'Normal text — AA (4.5:1)' => $contrastResult['aa_normal'],
                      'Large text — AA (3:1)' => $contrastResult['aa_large'],
                      'Normal text — AAA (7:1)' => $contrastResult['aaa_normal'],
                      'Large text — AAA (4.5:1)' => $contrastResult['aaa_large'],
                    ];
                  ?>
                  <?php foreach ($rows as $label => $passes): ?>
                  <li class="list-group-item d-flex align-items-center gap-2 px-0">
                    <span class="badge bg-<?= $passes ? 'success' : 'danger' ?>"><?= $passes ? '✓' : '✕' ?></span>
                    <?= clean_input($label) ?>
                  </li>
                  <?php endforeach; ?>
                </ul>
                <p class="mb-2">Want a full accessibility pass across your whole site?</p>
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
