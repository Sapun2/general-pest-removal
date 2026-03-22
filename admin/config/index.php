<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_DIR')) define('BASE_DIR', dirname(__DIR__, 2));
require_once BASE_DIR . '/admin/auth.php';
require_once BASE_DIR . '/includes/db.php';

$active_page = 'config';
$admin_title = 'Site Configuration';

if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error   = $_SESSION['flash_error']   ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// ── Load current config ──────────────────────────────────────────
$cfg = [];
if ($pdo) {
    try {
        $cfg = $pdo->query("SELECT config_key, config_value FROM site_config")
                   ->fetchAll(PDO::FETCH_KEY_PAIR);
    } catch (PDOException $e) {
        $flash_error = 'Could not load configuration. Run DB Setup first.';
    }
}
$c = fn(string $key, string $fallback = '') => $cfg[$key] ?? $fallback;

// ── Helper: upsert a set of keys ────────────────────────────────
function save_keys(PDO $pdo, array $keys, array $post, array $url_keys = []): void {
    $stmt = $pdo->prepare(
        "INSERT INTO site_config (config_key, config_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE config_value = VALUES(config_value)"
    );
    foreach ($keys as $key) {
        $value = trim($post[$key] ?? '');
        if (!in_array($key, $url_keys, true)) {
            $value = strip_tags($value);
        }
        $stmt->execute([$key, $value]);
    }
}

// ── Handle POST (section-by-section) ────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    if (($_POST['csrf_token'] ?? '') !== $_SESSION['csrf_token']) {
        $_SESSION['flash_error'] = 'Invalid request.';
        header('Location: /general-pest-removal/admin/config'); exit;
    }

    $section = $_POST['_section'] ?? '';

    try {
        switch ($section) {

            case 'business':
                save_keys($pdo, ['business_name', 'tagline'], $_POST);
                break;

            case 'logo':
                // Handle logo file upload — overrides URL field
                if (!empty($_FILES['logo_image_file']['tmp_name']) && $_FILES['logo_image_file']['error'] === UPLOAD_ERR_OK) {
                    $file    = $_FILES['logo_image_file'];
                    $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg','jpeg','png','webp','gif','svg'];
                    if (in_array($ext, $allowed, true) && $file['size'] <= 2 * 1024 * 1024) {
                        $upload_dir = BASE_DIR . '/assets/images/uploads';
                        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                        $filename = 'logo_' . time() . '.' . $ext;
                        if (move_uploaded_file($file['tmp_name'], $upload_dir . '/' . $filename)) {
                            $_POST['logo_image_url'] = '/assets/images/uploads/' . $filename;
                        }
                    }
                }
                save_keys($pdo, ['logo_type','logo_icon','logo_text_primary','logo_text_secondary','logo_image_url'], $_POST,
                          ['logo_image_url']);
                break;

            case 'phones':
                save_keys($pdo, ['phone_primary','phone_primary_raw','phone_secondary','phone_secondary_raw'], $_POST);
                break;

            case 'contact':
                save_keys($pdo, ['email_primary','email_admin','address','hours_weekday','hours_emergency'], $_POST);
                break;

            case 'social':
                save_keys($pdo, ['social_facebook','social_instagram','social_tiktok','social_google','social_yelp'], $_POST,
                          ['social_facebook','social_instagram','social_tiktok','social_google','social_yelp']);
                break;

            case 'email':
                save_keys($pdo, ['smtp_host','smtp_port','smtp_user','smtp_pass','smtp_encryption','smtp_from_name','smtp_from_email'], $_POST);
                break;

            case 'tracking':
                save_keys($pdo, ['gtm_id','ga4_id','gads_id','gads_label','meta_pixel_id','gsc_verification'], $_POST);
                break;
        }

        // Reload config
        $GLOBALS['site_config'] = load_site_config();
        $cfg = $pdo->query("SELECT config_key, config_value FROM site_config")->fetchAll(PDO::FETCH_KEY_PAIR);

        $section_labels = [
            'business' => 'Business Information',
            'logo'     => 'Logo',
            'phones'   => 'Phone Numbers',
            'contact'  => 'Contact & Hours',
            'social'   => 'Social Media',
            'email'    => 'Email / SMTP',
            'tracking' => 'Tracking Codes',
        ];
        $label = $section_labels[$section] ?? 'Settings';
        $_SESSION['flash_success'] = "{$label} saved successfully.";
        header('Location: /general-pest-removal/admin/config#' . $section); exit;

    } catch (PDOException $e) {
        $flash_error = 'Save failed: ' . $e->getMessage();
    }
}

// ── SMTP Test Send ───────────────────────────────────────────────
$test_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_section'] ?? '') === 'email_test') {
    if (($_POST['csrf_token'] ?? '') === $_SESSION['csrf_token']) {
        require_once BASE_DIR . '/includes/site-config.php';
        $GLOBALS['site_config'] = load_site_config();
        require_once BASE_DIR . '/includes/mailer.php';

        $test_to = trim($_POST['test_email'] ?? $c('email_admin'));
        if (filter_var($test_to, FILTER_VALIDATE_EMAIL)) {
            $ok = send_mail(
                $test_to, 'Admin',
                'SMTP Test — ' . $c('business_name', 'General Pest Removal'),
                mail_template('SMTP Test', '<p>Your SMTP configuration is working correctly. This is a test email sent from the admin panel.</p>'),
            );
            $test_result = $ok ? 'success' : 'error';
        } else {
            $test_result = 'invalid';
        }
    }
}

require_once BASE_DIR . '/admin/head.php';
require_once BASE_DIR . '/admin/sidebar.php';
?>
<main class="flex-grow p-8 overflow-auto">
<div class="max-w-4xl mx-auto">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Site Configuration</h1>
            <p class="text-sm text-gray-400 mt-1">Each section saves independently. Changes reflect immediately across all public pages.</p>
        </div>
        <a href="/general-pest-removal/" target="_blank"
           class="text-xs text-gray-400 hover:text-primary transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> View Website
        </a>
    </div>

    <?php if ($flash_success): ?>
    <div class="bg-yellow-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
        <i class="fa-solid fa-check-circle"></i> <?= htmlspecialchars($flash_success) ?>
    </div>
    <?php endif; ?>
    <?php if ($flash_error): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($flash_error) ?>
    </div>
    <?php endif; ?>

    <!-- ══════════════════════════════════════════════════════════ -->
    <!-- SECTION: Business Information                              -->
    <!-- ══════════════════════════════════════════════════════════ -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6" id="business">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="fa-solid fa-building text-primary text-sm"></i>
                </div>
                <h2 class="font-bold text-gray-900">Business Information</h2>
            </div>
        </div>
        <form method="POST" class="p-6 space-y-5">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="_section" value="business">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">
                        Business Name <span class="text-accent">*</span>
                    </label>
                    <input type="text" name="business_name" required
                           value="<?= htmlspecialchars($c('business_name', 'General Pest Removal')) ?>"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm transition">
                    <p class="text-xs text-gray-400 mt-1">Appears in browser tab, copyright, and emails.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">Tagline</label>
                    <input type="text" name="tagline"
                           value="<?= htmlspecialchars($c('tagline')) ?>"
                           placeholder="Sydney's Trusted Pest Removal Specialists"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm transition">
                    <p class="text-xs text-gray-400 mt-1">Shown in the footer brand column.</p>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-primary hover:bg-blue-900 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Save
                </button>
            </div>
        </form>
    </div>

    <!-- ══════════════════════════════════════════════════════════ -->
    <!-- SECTION: Logo                                              -->
    <!-- ══════════════════════════════════════════════════════════ -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6" id="logo">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-secondary/10 flex items-center justify-center">
                <i class="fa-solid fa-star text-secondary text-sm"></i>
            </div>
            <h2 class="font-bold text-gray-900">Logo</h2>
        </div>
        <form method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="_section" value="logo">

            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="logo_type" value="text" <?= $c('logo_type', 'text') !== 'image' ? 'checked' : '' ?> class="w-4 h-4 accent-primary" onchange="toggleLogoType(this.value)">
                    <span class="text-sm font-medium text-gray-700">Text + Icon</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="logo_type" value="image" <?= $c('logo_type') === 'image' ? 'checked' : '' ?> class="w-4 h-4 accent-primary" onchange="toggleLogoType(this.value)">
                    <span class="text-sm font-medium text-gray-700">Image Upload</span>
                </label>
            </div>

            <!-- Text logo -->
            <div id="logo-text-fields" class="space-y-4 <?= $c('logo_type') === 'image' ? 'hidden' : '' ?>">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">Icon Class</label>
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid <?= htmlspecialchars($c('logo_icon', 'fa-bug')) ?> text-white" id="logo-icon-preview-i"></i>
                            </div>
                            <input type="text" name="logo_icon" id="logo-icon-input"
                                   value="<?= htmlspecialchars($c('logo_icon', 'fa-bug')) ?>"
                                   placeholder="fa-bug"
                                   class="flex-grow px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm font-mono transition">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">Primary Text</label>
                        <input type="text" name="logo_text_primary"
                               value="<?= htmlspecialchars($c('logo_text_primary', 'Sydney')) ?>"
                               placeholder="Sydney"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">Secondary Text</label>
                        <input type="text" name="logo_text_secondary"
                               value="<?= htmlspecialchars($c('logo_text_secondary', 'Pest')) ?>"
                               placeholder="Pest"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm transition">
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-3">Preview</p>
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 bg-primary rounded-lg flex items-center justify-center">
                            <i class="fa-solid <?= htmlspecialchars($c('logo_icon', 'fa-bug')) ?> text-white text-sm" id="preview-icon-i"></i>
                        </div>
                        <span class="text-xl font-bold text-dark tracking-tight">
                            <span id="preview-primary"><?= htmlspecialchars($c('logo_text_primary', 'Sydney')) ?></span><span class="text-primary" id="preview-secondary"><?= htmlspecialchars($c('logo_text_secondary', 'Pest')) ?></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Image logo -->
            <div id="logo-image-fields" class="space-y-4 <?= $c('logo_type') !== 'image' ? 'hidden' : '' ?>">
                <?php $logo_url = $c('logo_image_url'); ?>
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Current Logo</p>
                    <div class="h-10 flex items-center">
                        <?php if ($logo_url): ?>
                        <img src="<?= htmlspecialchars($logo_url) ?>" alt="Current logo" class="max-h-full w-auto object-contain" id="logo-img-tag">
                        <?php else: ?>
                        <span class="text-sm text-gray-400 italic" id="logo-img-placeholder">No logo uploaded yet</span>
                        <img src="" alt="" class="max-h-full w-auto object-contain hidden" id="logo-img-tag">
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Upload New Logo</label>
                    <label class="flex items-center gap-3 cursor-pointer w-fit bg-white hover:bg-gray-50 border border-dashed border-gray-300 hover:border-primary px-5 py-3 rounded-xl transition group">
                        <div class="w-9 h-9 rounded-lg bg-primary/10 group-hover:bg-primary/20 flex items-center justify-center flex-shrink-0 transition">
                            <i class="fa-solid fa-arrow-up-from-bracket text-primary text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700 group-hover:text-primary transition">Choose file to upload</p>
                            <p class="text-xs text-gray-400 mt-0.5">PNG, SVG, WebP, JPG &middot; Max 2 MB</p>
                        </div>
                        <input type="file" name="logo_image_file" id="logo-file-input" accept="image/*,.svg" class="hidden" onchange="previewLogoUpload(this)">
                    </label>
                    <div id="logo-upload-preview-wrap" class="hidden mt-3 flex items-center gap-3 bg-yellow-50 border border-green-200 rounded-xl px-4 py-3">
                        <img id="logo-upload-preview-img" src="" alt="" class="max-h-8 w-auto object-contain">
                        <div class="flex-grow min-w-0">
                            <p id="logo-upload-filename" class="text-xs font-semibold text-gray-700 truncate"></p>
                            <p class="text-xs text-green-600 mt-0.5">Ready to upload — click Save below</p>
                        </div>
                        <button type="button" onclick="clearLogoUpload()" class="text-gray-400 hover:text-red-500 transition text-sm">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="border-t border-gray-100 pt-4">
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">Or Enter URL Directly</label>
                    <input type="text" name="logo_image_url" id="logo-image-url"
                           value="<?= htmlspecialchars($logo_url) ?>"
                           placeholder="/assets/images/logo.png or https://..."
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm transition font-mono">
                    <p class="text-xs text-gray-400 mt-1">File upload above takes precedence over this field.</p>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-primary hover:bg-blue-900 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Save
                </button>
            </div>
        </form>
    </div>

    <!-- ══════════════════════════════════════════════════════════ -->
    <!-- SECTION: Phone Numbers                                     -->
    <!-- ══════════════════════════════════════════════════════════ -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6" id="phones">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-accent/10 flex items-center justify-center">
                <i class="fa-solid fa-phone text-accent text-sm"></i>
            </div>
            <h2 class="font-bold text-gray-900">Phone Numbers</h2>
        </div>
        <form method="POST" class="p-6 space-y-5">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="_section" value="phones">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">Primary Phone <span class="text-accent">*</span></label>
                    <input type="text" name="phone_primary" required value="<?= htmlspecialchars($c('phone_primary', '(07) 3155 0198')) ?>"
                           placeholder="(07) 3155 0198"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm transition">
                    <p class="text-xs text-gray-400 mt-1">Display format, e.g. (07) 3155 0198</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">Primary Phone Raw <span class="text-accent">*</span></label>
                    <input type="text" name="phone_primary_raw" required value="<?= htmlspecialchars($c('phone_primary_raw', '+61281550198')) ?>"
                           placeholder="+61281550198"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm font-mono transition">
                    <p class="text-xs text-gray-400 mt-1">International format for tel: links, e.g. +14165550198</p>
                </div>
            </div>
            <div class="border-t border-gray-100 pt-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">Secondary Phone <span class="font-normal text-gray-400">(optional)</span></p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">Display Format</label>
                        <input type="text" name="phone_secondary" value="<?= htmlspecialchars($c('phone_secondary')) ?>"
                               placeholder="(647) 555-0199"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">Raw Format</label>
                        <input type="text" name="phone_secondary_raw" value="<?= htmlspecialchars($c('phone_secondary_raw')) ?>"
                               placeholder="+16475550199"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm font-mono transition">
                    </div>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-primary hover:bg-blue-900 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Save
                </button>
            </div>
        </form>
    </div>

    <!-- ══════════════════════════════════════════════════════════ -->
    <!-- SECTION: Contact & Hours                                   -->
    <!-- ══════════════════════════════════════════════════════════ -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6" id="contact">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                <i class="fa-solid fa-envelope text-primary text-sm"></i>
            </div>
            <h2 class="font-bold text-gray-900">Contact &amp; Hours</h2>
        </div>
        <form method="POST" class="p-6 space-y-5">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="_section" value="contact">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">Public Email <span class="text-accent">*</span></label>
                    <input type="email" name="email_primary" required value="<?= htmlspecialchars($c('email_primary', 'info@generalpestremoval.com')) ?>"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm transition">
                    <p class="text-xs text-gray-400 mt-1">Shown in header, footer, and contact page.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">Admin Notification Email</label>
                    <input type="email" name="email_admin" value="<?= htmlspecialchars($c('email_admin')) ?>"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm transition">
                    <p class="text-xs text-gray-400 mt-1">Booking &amp; contact alerts are sent here.</p>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">Business Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($c('address', 'Sydney, NSW, Australia')) ?>"
                       placeholder="Sydney, NSW, Australia"
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm transition">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">Weekday Hours</label>
                    <input type="text" name="hours_weekday" value="<?= htmlspecialchars($c('hours_weekday', 'Mon–Sat, 7am–8pm')) ?>"
                           placeholder="Mon–Sat, 7am–8pm"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">Emergency / 24-7 Label</label>
                    <input type="text" name="hours_emergency" value="<?= htmlspecialchars($c('hours_emergency', '24/7 Emergency Line')) ?>"
                           placeholder="24/7 Emergency Line"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm transition">
                    <p class="text-xs text-gray-400 mt-1">Also shown in the header top bar.</p>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-primary hover:bg-blue-900 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Save
                </button>
            </div>
        </form>
    </div>

    <!-- ══════════════════════════════════════════════════════════ -->
    <!-- SECTION: Social Media                                      -->
    <!-- ══════════════════════════════════════════════════════════ -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6" id="social">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                <i class="fa-brands fa-instagram text-blue-600 text-sm"></i>
            </div>
            <h2 class="font-bold text-gray-900">Social Media Links</h2>
        </div>
        <form method="POST" class="p-6">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="_section" value="social">
            <p class="text-xs text-gray-400 mb-5">Leave blank to hide that platform from the footer. Enter full URLs including https://</p>
            <div class="space-y-4">
                <?php
                $social_fields = [
                    ['key' => 'social_facebook',  'icon' => 'fa-facebook',  'color' => 'text-blue-600', 'label' => 'Facebook',  'placeholder' => 'https://facebook.com/yourpage'],
                    ['key' => 'social_instagram',  'icon' => 'fa-instagram', 'color' => 'text-pink-500', 'label' => 'Instagram', 'placeholder' => 'https://instagram.com/yourhandle'],
                    ['key' => 'social_tiktok',    'icon' => 'fa-tiktok',    'color' => 'text-gray-900', 'label' => 'TikTok',    'placeholder' => 'https://tiktok.com/@yourhandle'],
                    ['key' => 'social_google',    'icon' => 'fa-google',    'color' => 'text-red-500',  'label' => 'Google',    'placeholder' => 'https://g.page/yourprofile'],
                    ['key' => 'social_yelp',      'icon' => 'fa-yelp',      'color' => 'text-red-600',  'label' => 'Yelp',      'placeholder' => 'https://yelp.com/biz/yourprofile'],
                ];
                foreach ($social_fields as $sf): ?>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-center flex-shrink-0">
                        <i class="fa-brands <?= $sf['icon'] ?> <?= $sf['color'] ?>"></i>
                    </div>
                    <div class="flex-grow">
                        <label class="block text-xs font-semibold text-gray-600 mb-1"><?= $sf['label'] ?></label>
                        <input type="url" name="<?= $sf['key'] ?>"
                               value="<?= htmlspecialchars($c($sf['key'])) ?>"
                               placeholder="<?= htmlspecialchars($sf['placeholder']) ?>"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm transition">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="flex justify-end mt-6">
                <button type="submit" class="bg-primary hover:bg-blue-900 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Save
                </button>
            </div>
        </form>
    </div>

    <!-- ══════════════════════════════════════════════════════════ -->
    <!-- SECTION: Email / SMTP                                      -->
    <!-- ══════════════════════════════════════════════════════════ -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6" id="email">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                <i class="fa-solid fa-paper-plane text-green-600 text-sm"></i>
            </div>
            <div>
                <h2 class="font-bold text-gray-900">Email / SMTP</h2>
                <p class="text-xs text-gray-400 mt-0.5">Booking and contact form alerts are sent via these settings.</p>
            </div>
        </div>

        <!-- Info banner -->
        <div class="mx-6 mt-5 bg-blue-50 border border-blue-200 rounded-lg p-4 flex gap-3">
            <i class="fa-solid fa-circle-info text-blue-500 mt-0.5 flex-shrink-0"></i>
            <div class="text-sm text-blue-800 space-y-1">
                <p class="font-semibold">Common SMTP providers:</p>
                <ul class="text-xs space-y-0.5 text-blue-700">
                    <li><strong>Gmail:</strong> smtp.gmail.com &nbsp;|&nbsp; Port 587, TLS &nbsp;|&nbsp; Use App Password (2FA required)</li>
                    <li><strong>Outlook/Office 365:</strong> smtp.office365.com &nbsp;|&nbsp; Port 587, TLS</li>
                    <li><strong>SendGrid:</strong> smtp.sendgrid.net &nbsp;|&nbsp; Port 587, TLS &nbsp;|&nbsp; Username: apikey</li>
                    <li><strong>Mailgun:</strong> smtp.mailgun.org &nbsp;|&nbsp; Port 587, TLS</li>
                    <li><strong>cPanel / Hosting:</strong> mail.yourdomain.com &nbsp;|&nbsp; Port 465, SSL</li>
                </ul>
            </div>
        </div>

        <form method="POST" class="p-6 space-y-5">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="_section" value="email">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">SMTP Host</label>
                    <input type="text" name="smtp_host" value="<?= htmlspecialchars($c('smtp_host')) ?>"
                           placeholder="smtp.gmail.com"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm font-mono transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">Port</label>
                    <input type="number" name="smtp_port" value="<?= htmlspecialchars($c('smtp_port', '587')) ?>"
                           placeholder="587"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm font-mono transition">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">SMTP Username</label>
                    <input type="text" name="smtp_user" value="<?= htmlspecialchars($c('smtp_user')) ?>"
                           placeholder="you@gmail.com"
                           autocomplete="off"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">SMTP Password / App Password</label>
                    <div class="relative">
                        <input type="password" name="smtp_pass" id="smtp-pass" value="<?= htmlspecialchars($c('smtp_pass')) ?>"
                               placeholder="••••••••••••••••"
                               autocomplete="new-password"
                               class="w-full px-4 py-2.5 pr-12 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm transition">
                        <button type="button" onclick="togglePass()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                            <i class="fa-solid fa-eye" id="pass-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">Encryption</label>
                    <select name="smtp_encryption" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm transition bg-white">
                        <option value="tls"  <?= $c('smtp_encryption','tls') === 'tls'  ? 'selected' : '' ?>>TLS (recommended, port 587)</option>
                        <option value="ssl"  <?= $c('smtp_encryption','tls') === 'ssl'  ? 'selected' : '' ?>>SSL (port 465)</option>
                        <option value="none" <?= $c('smtp_encryption','tls') === 'none' ? 'selected' : '' ?>>None (not recommended)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">From Name</label>
                    <input type="text" name="smtp_from_name" value="<?= htmlspecialchars($c('smtp_from_name')) ?>"
                           placeholder="General Pest Removal"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">From Email</label>
                    <input type="email" name="smtp_from_email" value="<?= htmlspecialchars($c('smtp_from_email')) ?>"
                           placeholder="noreply@yourdomain.ca"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm transition">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-primary hover:bg-blue-900 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Save SMTP Settings
                </button>
            </div>
        </form>

        <!-- Test Send -->
        <div class="border-t border-gray-100 px-6 py-5">
            <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-3">Send a Test Email</p>
            <?php if ($test_result === 'success'): ?>
            <div class="bg-yellow-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-lg mb-3 flex items-center gap-2">
                <i class="fa-solid fa-check-circle"></i> Test email sent successfully! Check your inbox.
            </div>
            <?php elseif ($test_result === 'error'): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg mb-3 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i> Send failed. Double-check your SMTP settings and try again. Check PHP error log for details.
            </div>
            <?php elseif ($test_result === 'invalid'): ?>
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm px-4 py-3 rounded-lg mb-3 flex items-center gap-2">
                <i class="fa-solid fa-exclamation-circle"></i> Please enter a valid email address.
            </div>
            <?php endif; ?>
            <form method="POST" class="flex items-end gap-3">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="_section" value="email_test">
                <div class="flex-grow max-w-xs">
                    <label class="block text-xs text-gray-500 mb-1">Send test to:</label>
                    <input type="email" name="test_email" value="<?= htmlspecialchars($c('email_admin')) ?>"
                           placeholder="admin@yourdomain.ca"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm transition">
                </div>
                <button type="submit" class="bg-secondary hover:bg-green-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Send Test
                </button>
            </form>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════ -->
    <!-- SECTION: Tracking Codes                                    -->
    <!-- ══════════════════════════════════════════════════════════ -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-8" id="tracking">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center">
                <i class="fa-solid fa-chart-line text-orange-600 text-sm"></i>
            </div>
            <div>
                <h2 class="font-bold text-gray-900">Tracking Codes</h2>
                <p class="text-xs text-gray-400 mt-0.5">All codes auto-inject into every page &lt;head&gt;. Leave blank to disable.</p>
            </div>
        </div>
        <form method="POST" class="p-6 space-y-6">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="_section" value="tracking">

            <!-- GTM -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-start">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 border border-blue-200 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fa-brands fa-google text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Google Tag Manager</p>
                        <p class="text-xs text-gray-400">Manages all tags from one place. Add GTM-XXXXXXX</p>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <input type="text" name="gtm_id" value="<?= htmlspecialchars($c('gtm_id')) ?>"
                           placeholder="GTM-XXXXXXX"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm font-mono transition">
                    <p class="text-xs text-gray-400 mt-1">Found in Google Tag Manager under your container.</p>
                </div>
            </div>
            <hr class="border-gray-100">

            <!-- GA4 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-start">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-orange-50 border border-orange-200 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fa-brands fa-google text-orange-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Google Analytics 4</p>
                        <p class="text-xs text-gray-400">Track website visitors and behaviour. Add G-XXXXXXXXXX</p>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <input type="text" name="ga4_id" value="<?= htmlspecialchars($c('ga4_id')) ?>"
                           placeholder="G-XXXXXXXXXX"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm font-mono transition">
                    <p class="text-xs text-gray-400 mt-1">Analytics → Admin → Data Streams → Measurement ID.</p>
                </div>
            </div>
            <hr class="border-gray-100">

            <!-- Google Ads -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-start">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-yellow-50 border border-green-200 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fa-brands fa-google text-green-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Google Ads</p>
                        <p class="text-xs text-gray-400">Conversion tracking for paid campaigns. Add AW-XXXXXXXXXX</p>
                    </div>
                </div>
                <div class="md:col-span-2 space-y-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Conversion ID</label>
                        <input type="text" name="gads_id" value="<?= htmlspecialchars($c('gads_id')) ?>"
                               placeholder="AW-XXXXXXXXXX"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm font-mono transition">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Conversion Label <span class="text-gray-400">(optional)</span></label>
                        <input type="text" name="gads_label" value="<?= htmlspecialchars($c('gads_label')) ?>"
                               placeholder="xxxxxxxxxxxxxxxxxxx"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm font-mono transition">
                        <p class="text-xs text-gray-400 mt-1">Google Ads → Tools → Conversions → your event's "Tag details".</p>
                    </div>
                </div>
            </div>
            <hr class="border-gray-100">

            <!-- Meta Pixel -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-start">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 border border-blue-200 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fa-brands fa-meta text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Meta Pixel</p>
                        <p class="text-xs text-gray-400">Track Facebook & Instagram ad conversions.</p>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <input type="text" name="meta_pixel_id" value="<?= htmlspecialchars($c('meta_pixel_id')) ?>"
                           placeholder="123456789012345"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm font-mono transition">
                    <p class="text-xs text-gray-400 mt-1">Meta Events Manager → Data Sources → your Pixel ID.</p>
                </div>
            </div>
            <hr class="border-gray-100">

            <!-- Google Search Console -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-start">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-teal-50 border border-teal-200 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fa-brands fa-google text-teal-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Google Search Console</p>
                        <p class="text-xs text-gray-400">Paste only the <em>content="..."</em> value, not the full tag.</p>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <input type="text" name="gsc_verification" value="<?= htmlspecialchars($c('gsc_verification')) ?>"
                           placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-sm font-mono transition">
                    <p class="text-xs text-gray-400 mt-1">GSC → Add Property → HTML tag → copy only the value inside content="".</p>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-primary hover:bg-blue-900 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Save Tracking Codes
                </button>
            </div>
        </form>
    </div>

</div>
</main>

<script>
// ── Logo type toggle ─────────────────────────────────────────────
function toggleLogoType(val) {
    document.getElementById('logo-text-fields').classList.toggle('hidden', val === 'image');
    document.getElementById('logo-image-fields').classList.toggle('hidden', val !== 'image');
}

// ── Icon live preview ────────────────────────────────────────────
document.getElementById('logo-icon-input').addEventListener('input', function() {
    var c = 'fa-solid ' + this.value.trim() + ' text-white';
    document.getElementById('logo-icon-preview-i').className = c;
    document.getElementById('preview-icon-i').className = 'fa-solid ' + this.value.trim() + ' text-white text-sm';
});
document.querySelector('input[name="logo_text_primary"]').addEventListener('input', function() {
    document.getElementById('preview-primary').textContent = this.value;
});
document.querySelector('input[name="logo_text_secondary"]').addEventListener('input', function() {
    document.getElementById('preview-secondary').textContent = this.value;
});

// ── Image URL live preview ───────────────────────────────────────
document.getElementById('logo-image-url').addEventListener('input', function() {
    var url = this.value.trim();
    var img = document.getElementById('logo-img-tag');
    var ph  = document.getElementById('logo-img-placeholder');
    if (url) { img.src = url; img.classList.remove('hidden'); if(ph) ph.classList.add('hidden'); }
    else     { img.classList.add('hidden'); if(ph) ph.classList.remove('hidden'); }
});

// ── Logo file upload preview ─────────────────────────────────────
function previewLogoUpload(input) {
    var file = input.files[0];
    if (!file) return;
    var wrap = document.getElementById('logo-upload-preview-wrap');
    var img  = document.getElementById('logo-upload-preview-img');
    document.getElementById('logo-upload-filename').textContent = file.name;
    var url = URL.createObjectURL(file);
    img.src = url; wrap.classList.remove('hidden');
}
function clearLogoUpload() {
    document.getElementById('logo-file-input').value = '';
    document.getElementById('logo-upload-preview-wrap').classList.add('hidden');
    document.getElementById('logo-upload-preview-img').src = '';
}

// ── SMTP password toggle ─────────────────────────────────────────
function togglePass() {
    var inp = document.getElementById('smtp-pass');
    var eye = document.getElementById('pass-eye');
    if (inp.type === 'password') {
        inp.type = 'text';
        eye.className = 'fa-solid fa-eye-slash';
    } else {
        inp.type = 'password';
        eye.className = 'fa-solid fa-eye';
    }
}
</script>
</body>
</html>
