<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_DIR')) define('BASE_DIR', dirname(__DIR__, 2));
require_once BASE_DIR . '/admin/auth.php';
require_once BASE_DIR . '/includes/db.php';

$active_page = 'services';
$admin_title = 'Service';

if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$service  = null;
$is_edit  = false;
$success  = '';
$error    = '';

// Load existing service if editing
if (isset($_GET['id']) && $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$_GET['id']]);
        $service  = $stmt->fetch(PDO::FETCH_ASSOC);
        $is_edit  = (bool) $service;
        if ($service && $service['features']) {
            $service['features_decoded'] = json_decode($service['features'], true) ?: [];
        } else {
            $service['features_decoded'] = [];
        }
    } catch (PDOException $e) { $error = "Could not load service."; }
}

// ── Image upload helper ──────────────────────────────────
function handle_image_upload_svc(string $field_name, string $upload_dir): string
{
    if (empty($_FILES[$field_name]['tmp_name'])) return '';
    $file    = $_FILES[$field_name];
    $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp','gif'];
    if (!in_array($ext, $allowed, true))  return '';
    if ($file['size'] > 5 * 1024 * 1024) return '';
    if ($file['error'] !== UPLOAD_ERR_OK) return '';
    $filename = uniqid('img_', true) . '.' . $ext;
    $dest     = $upload_dir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) return '';
    return '/assets/images/uploads/' . $filename;
}

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    if (($_POST['csrf_token'] ?? '') !== $_SESSION['csrf_token']) {
        $_SESSION['flash_error'] = 'Invalid request.';
        header('Location: /admin/services'); exit;
    }

    $upload_dir   = BASE_DIR . '/assets/images/uploads';
    $image_upload = handle_image_upload_svc('image_file', $upload_dir);

    $name = trim(strip_tags($_POST['name'] ?? ''));
    $slug = preg_replace('/[^a-z0-9-]/', '', strtolower(trim(str_replace(' ', '-', $_POST['slug'] ?? ''))));

    if (empty($name) || empty($slug)) {
        $error = "Name and Slug are required.";
    } else {
        // Collect features from indexed POST fields
        $features = [];
        $i = 0;
        while (isset($_POST['feat_icon_' . $i]) || isset($_POST['feat_title_' . $i])) {
            $feat_icon  = trim(strip_tags($_POST['feat_icon_'  . $i] ?? ''));
            $feat_title = trim(strip_tags($_POST['feat_title_' . $i] ?? ''));
            $feat_desc  = trim(strip_tags($_POST['feat_desc_'  . $i] ?? ''));
            if ($feat_title !== '') {
                $features[] = [
                    'icon'  => $feat_icon,
                    'title' => $feat_title,
                    'desc'  => $feat_desc,
                ];
            }
            $i++;
        }

        $data = [
            'name'             => $name,
            'slug'             => $slug,
            'tagline'          => trim(strip_tags($_POST['tagline']          ?? '')),
            'description'      => trim($_POST['description']                 ?? ''), // HTML allowed
            'icon'             => trim(strip_tags($_POST['icon']             ?? 'fa-shield-bug')),
            'badge_text'       => trim(strip_tags($_POST['badge_text']       ?? '')),
            'image_path'       => $image_upload ?: trim($_POST['image_path'] ?? ''),
            'features'         => json_encode($features),
            'link_anchor'      => trim(strip_tags($_POST['link_anchor']      ?? '')),
            'sort_order'       => (int)($_POST['sort_order']                 ?? 0),
            'is_active'        => isset($_POST['is_active']) ? 1 : 0,
            'meta_title'       => trim(strip_tags($_POST['meta_title']       ?? '')),
            'meta_description' => trim(strip_tags($_POST['meta_description'] ?? '')),
        ];

        try {
            if ($is_edit && $service) {
                $sql = "UPDATE services SET
                            name=:name, slug=:slug, tagline=:tagline, description=:description,
                            icon=:icon, badge_text=:badge_text, image_path=:image_path,
                            features=:features, link_anchor=:link_anchor, sort_order=:sort_order,
                            is_active=:is_active, meta_title=:meta_title, meta_description=:meta_description
                        WHERE id=:id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array_merge($data, [':id' => $service['id']]));
                $success = "Service updated successfully.";
                // Reload
                $stmt2 = $pdo->prepare("SELECT * FROM services WHERE id = ? LIMIT 1");
                $stmt2->execute([$service['id']]);
                $service = $stmt2->fetch(PDO::FETCH_ASSOC);
                $service['features_decoded'] = json_decode($service['features'] ?? '[]', true) ?: [];
            } else {
                $sql = "INSERT INTO services
                            (name, slug, tagline, description, icon, badge_text, image_path, features, link_anchor, sort_order, is_active, meta_title, meta_description)
                        VALUES
                            (:name, :slug, :tagline, :description, :icon, :badge_text, :image_path, :features, :link_anchor, :sort_order, :is_active, :meta_title, :meta_description)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($data);
                $new_id = $pdo->lastInsertId();
                $_SESSION['flash_success'] = 'Service created successfully.';
                header("Location: /admin/services/edit?id={$new_id}&saved=1");
                exit;
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "That slug is already in use. Please choose a different slug.";
            } else {
                $error = "Save failed: " . $e->getMessage();
            }
        }
    }
}

if (isset($_GET['saved'])) {
    $success = "Service created successfully.";
}

$admin_title   = $is_edit ? 'Edit Service' : 'New Service';
$features_list = $service['features_decoded'] ?? [];

require_once BASE_DIR . '/admin/head.php';
require_once BASE_DIR . '/admin/sidebar.php';
?>

<main class="flex-grow p-8">
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="/admin/services" class="text-gray-400 hover:text-primary transition">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-900"><?= $is_edit ? 'Edit Service' : 'New Service' ?></h1>
    </div>

    <?php if ($success): ?>
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
        <i class="fa-solid fa-check-circle"></i> <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-6" id="service-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <!-- Basic Info -->
        <div class="bg-white rounded-xl shadow-sm p-6 space-y-5">
            <h2 class="font-bold text-gray-700 text-sm uppercase tracking-wide border-b pb-3">Basic Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Service Name *</label>
                    <input type="text" name="name" id="svc-name" required
                           value="<?= htmlspecialchars($service['name'] ?? '') ?>"
                           placeholder="e.g., Bed Bug Extermination"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">URL Slug *</label>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-400 text-sm">#</span>
                        <input type="text" name="slug" id="svc-slug" required
                               value="<?= htmlspecialchars($service['slug'] ?? '') ?>"
                               placeholder="bedbugs"
                               class="flex-grow px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm font-mono transition">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Used as anchor on the services page. Auto-generated from name.</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tagline <span class="text-gray-400 font-normal">(short subtitle)</span></label>
                <input type="text" name="tagline"
                       value="<?= htmlspecialchars($service['tagline'] ?? '') ?>"
                       placeholder="e.g., Chemical-free, single-visit heat treatments"
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm transition">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Description <span class="text-gray-400 font-normal">(HTML allowed)</span></label>
                <textarea name="description" rows="5"
                          placeholder="<p>Full description of this service...</p>"
                          class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm font-mono transition"><?= htmlspecialchars($service['description'] ?? '') ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Icon <span class="text-gray-400 font-normal">(Font Awesome class)</span></label>
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center flex-shrink-0" id="icon-preview">
                            <i class="fa-solid <?= htmlspecialchars($service['icon'] ?? 'fa-shield-bug') ?> text-accent" id="icon-preview-i"></i>
                        </div>
                        <input type="text" name="icon" id="icon-input"
                               value="<?= htmlspecialchars($service['icon'] ?? 'fa-shield-bug') ?>"
                               placeholder="fa-shield-bug"
                               class="flex-grow px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm font-mono transition">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">e.g. fa-fire, fa-bug, fa-house-crack</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Badge Text <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input type="text" name="badge_text"
                           value="<?= htmlspecialchars($service['badge_text'] ?? '') ?>"
                           placeholder="e.g., Most Popular"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Link Anchor <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input type="text" name="link_anchor"
                           value="<?= htmlspecialchars($service['link_anchor'] ?? '') ?>"
                           placeholder="e.g., #bedbugs"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm font-mono transition">
                    <p class="text-xs text-gray-400 mt-1">Anchor link on the public services page.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Service Image</label>
                    <?php $img_val = htmlspecialchars($service['image_path'] ?? ''); ?>
                    <!-- Preview -->
                    <div id="svc-img-preview-wrap" class="<?= $img_val ? '' : 'hidden' ?> mb-2 relative w-40 h-28 rounded-lg overflow-hidden border border-gray-200 bg-gray-50">
                        <img id="svc-img-preview" src="<?= $img_val ?>" alt="Service image preview" class="w-full h-full object-cover">
                        <button type="button" onclick="clearSvcImage()"
                                class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full text-xs flex items-center justify-center hover:bg-red-700">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                    <!-- Upload -->
                    <label class="flex items-center gap-2 cursor-pointer w-fit bg-gray-100 hover:bg-gray-200 border border-dashed border-gray-300 px-4 py-2.5 rounded-lg transition mb-2">
                        <i class="fa-solid fa-upload text-gray-500 text-sm"></i>
                        <span class="text-sm text-gray-600 font-medium">Upload image</span>
                        <input type="file" name="image_file" id="svc-img-file" accept="image/*"
                               class="hidden" onchange="previewSvcImage(this)">
                    </label>
                    <p class="text-xs text-gray-400 mb-1">Or enter a path manually:</p>
                    <input type="text" name="image_path" id="svc-img-path"
                           value="<?= $img_val ?>"
                           placeholder="/assets/images/service.png"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm transition"
                           oninput="updateSvcPreview(this.value)">
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP · Max 5 MB</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" min="0"
                           value="<?= (int)($service['sort_order'] ?? 0) ?>"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm transition">
                    <p class="text-xs text-gray-400 mt-1">Lower number = displayed first.</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       <?= !$is_edit || !empty($service['is_active']) ? 'checked' : '' ?>
                       class="w-4 h-4">
                <label for="is_active" class="text-sm font-bold text-gray-700 cursor-pointer">
                    Active <span class="text-gray-400 font-normal">(uncheck to hide from public website)</span>
                </label>
            </div>
        </div>

        <!-- Features -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between border-b pb-3 mb-5">
                <h2 class="font-bold text-gray-700 text-sm uppercase tracking-wide">Service Features</h2>
                <button type="button" id="add-feature-btn"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold px-4 py-1.5 rounded-lg transition flex items-center gap-1">
                    <i class="fa-solid fa-plus"></i> Add Feature
                </button>
            </div>
            <p class="text-xs text-gray-400 mb-4">These appear as bullet-point highlights on the service listing. Leave title blank to exclude a row.</p>

            <div id="features-container" class="space-y-3">
                <?php if (empty($features_list)): ?>
                <!-- Default empty row -->
                <div class="feature-row flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                    <div class="flex-grow grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Icon class</label>
                            <input type="text" name="feat_icon_0" placeholder="fa-fire"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm font-mono focus:ring-2 focus:ring-primary outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Feature Title *</label>
                            <input type="text" name="feat_title_0" placeholder="Thermal Heat Treatments"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-primary outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Short Description</label>
                            <input type="text" name="feat_desc_0" placeholder="Chemical-free, single-visit elimination."
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-primary outline-none">
                        </div>
                    </div>
                    <button type="button" onclick="removeFeature(this)"
                            class="mt-5 text-red-400 hover:text-red-700 transition flex-shrink-0">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
                <?php else: ?>
                <?php foreach ($features_list as $fi => $feat): ?>
                <div class="feature-row flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                    <div class="flex-grow grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Icon class</label>
                            <input type="text" name="feat_icon_<?= $fi ?>" value="<?= htmlspecialchars($feat['icon'] ?? '') ?>" placeholder="fa-fire"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm font-mono focus:ring-2 focus:ring-primary outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Feature Title *</label>
                            <input type="text" name="feat_title_<?= $fi ?>" value="<?= htmlspecialchars($feat['title'] ?? '') ?>" placeholder="Thermal Heat Treatments"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-primary outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Short Description</label>
                            <input type="text" name="feat_desc_<?= $fi ?>" value="<?= htmlspecialchars($feat['desc'] ?? '') ?>" placeholder="Chemical-free, single-visit elimination."
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-primary outline-none">
                        </div>
                    </div>
                    <button type="button" onclick="removeFeature(this)"
                            class="mt-5 text-red-400 hover:text-red-700 transition flex-shrink-0">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- SEO -->
        <div class="bg-white rounded-xl shadow-sm p-6 space-y-5">
            <h2 class="font-bold text-gray-700 text-sm uppercase tracking-wide border-b pb-3">SEO Settings</h2>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Meta Title <span class="text-gray-400 font-normal" id="svc-meta-title-count">(<?= strlen($service['meta_title'] ?? '') ?>/60)</span>
                </label>
                <input type="text" name="meta_title" maxlength="70"
                       value="<?= htmlspecialchars($service['meta_title'] ?? '') ?>"
                       placeholder="Leave blank to auto-generate from service name"
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm transition"
                       oninput="document.getElementById('svc-meta-title-count').textContent='('+this.value.length+'/60)'">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Meta Description <span class="text-gray-400 font-normal" id="svc-meta-desc-count">(<?= strlen($service['meta_description'] ?? '') ?>/160)</span>
                </label>
                <textarea name="meta_description" rows="3" maxlength="180"
                          placeholder="Leave blank to use tagline"
                          class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm transition"
                          oninput="document.getElementById('svc-meta-desc-count').textContent='('+this.value.length+'/160)'"><?= htmlspecialchars($service['meta_description'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="flex justify-between">
            <a href="/admin/services" class="text-gray-400 hover:text-gray-600 py-2.5 transition">← Back to Services</a>
            <button type="submit" class="bg-primary hover:bg-blue-900 text-white font-bold px-10 py-2.5 rounded-lg transition">
                <?= $is_edit ? 'Save Changes' : 'Create Service' ?>
            </button>
        </div>
    </form>
</div>
</main>

<script>
// Service image upload helpers
function previewSvcImage(input) {
    var file = input.files[0];
    if (!file) return;
    var wrap = document.getElementById('svc-img-preview-wrap');
    var img  = document.getElementById('svc-img-preview');
    var reader = new FileReader();
    reader.onload = function (e) { img.src = e.target.result; wrap.classList.remove('hidden'); };
    reader.readAsDataURL(file);
}
function updateSvcPreview(val) {
    var wrap = document.getElementById('svc-img-preview-wrap');
    var img  = document.getElementById('svc-img-preview');
    if (val) { img.src = val; wrap.classList.remove('hidden'); }
    else     { wrap.classList.add('hidden'); }
}
function clearSvcImage() {
    document.getElementById('svc-img-preview-wrap').classList.add('hidden');
    document.getElementById('svc-img-preview').src = '';
    document.getElementById('svc-img-path').value = '';
    document.getElementById('svc-img-file').value = '';
}

// Auto-generate slug from name
document.getElementById('svc-name').addEventListener('input', function () {
    var slugField = document.getElementById('svc-slug');
    if (!slugField.dataset.manuallyEdited) {
        slugField.value = this.value
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }
});
document.getElementById('svc-slug').addEventListener('input', function () {
    this.dataset.manuallyEdited = '1';
});

// Icon preview
document.getElementById('icon-input').addEventListener('input', function () {
    var iconEl = document.getElementById('icon-preview-i');
    iconEl.className = 'fa-solid ' + this.value.trim() + ' text-accent';
});

// Features: track row count
var featureCount = document.querySelectorAll('.feature-row').length;

function addFeature() {
    var idx = featureCount;
    var row = document.createElement('div');
    row.className = 'feature-row flex items-start gap-3 p-3 bg-gray-50 rounded-lg';
    row.innerHTML = '<div class="flex-grow grid grid-cols-1 md:grid-cols-3 gap-3">'
        + '<div><label class="block text-xs text-gray-500 mb-1">Icon class</label>'
        + '<input type="text" name="feat_icon_' + idx + '" placeholder="fa-fire" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm font-mono focus:ring-2 focus:ring-primary outline-none"></div>'
        + '<div><label class="block text-xs text-gray-500 mb-1">Feature Title *</label>'
        + '<input type="text" name="feat_title_' + idx + '" placeholder="Feature name" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-primary outline-none"></div>'
        + '<div><label class="block text-xs text-gray-500 mb-1">Short Description</label>'
        + '<input type="text" name="feat_desc_' + idx + '" placeholder="Brief description." class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-primary outline-none"></div>'
        + '</div>'
        + '<button type="button" onclick="removeFeature(this)" class="mt-5 text-red-400 hover:text-red-700 transition flex-shrink-0"><i class="fa-solid fa-times"></i></button>';
    document.getElementById('features-container').appendChild(row);
    featureCount++;
}

function removeFeature(btn) {
    var row = btn.closest('.feature-row');
    row.parentNode.removeChild(row);
    // Re-index remaining rows
    var rows = document.querySelectorAll('.feature-row');
    rows.forEach(function (r, newIdx) {
        r.querySelectorAll('input[name^="feat_"]').forEach(function (inp) {
            inp.name = inp.name.replace(/_(icon|title|desc)_\d+$/, '_$1_' + newIdx);
        });
    });
    featureCount = rows.length;
}

document.getElementById('add-feature-btn').addEventListener('click', addFeature);
</script>
</body>
</html>
