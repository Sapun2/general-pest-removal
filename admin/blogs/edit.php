<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_DIR')) define('BASE_DIR', dirname(__DIR__, 2));
require_once BASE_DIR . '/admin/auth.php';
require_once BASE_DIR . '/includes/db.php';

$active_page = 'blogs';
$admin_title = 'Blog Post';

if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$post     = null;
$is_edit  = false;
$success  = '';
$error    = '';

// Load existing post if editing
if (isset($_GET['id']) && $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$_GET['id']]);
        $post    = $stmt->fetch(PDO::FETCH_ASSOC);
        $is_edit = (bool) $post;
    } catch (PDOException $e) { $error = "Could not load post."; }
}

// ── Image upload helper ──────────────────────────────────
function handle_image_upload(string $field_name, string $upload_dir): string
{
    if (empty($_FILES[$field_name]['tmp_name'])) return '';
    $file     = $_FILES[$field_name];
    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed  = ['jpg','jpeg','png','webp','gif'];
    if (!in_array($ext, $allowed, true))       return '';
    if ($file['size'] > 5 * 1024 * 1024)       return ''; // 5 MB max
    if ($file['error'] !== UPLOAD_ERR_OK)       return '';
    $filename = uniqid('img_', true) . '.' . $ext;
    $dest     = $upload_dir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) return '';
    return '/assets/images/uploads/' . $filename;
}

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    if (($_POST['csrf_token'] ?? '') !== $_SESSION['csrf_token']) {
        $_SESSION['flash_error'] = 'Invalid request.';
        header('Location: /general-pest-removal/admin/blogs'); exit;
    }

    $upload_dir = BASE_DIR . '/assets/images/uploads';

    // Handle file uploads — overrides text field if a file was selected
    $featured_image_upload = handle_image_upload('featured_image_file', $upload_dir);
    $og_image_upload       = handle_image_upload('og_image_file',       $upload_dir);

    $data = [
        'slug'             => preg_replace('/[^a-z0-9-]/', '', strtolower(trim(str_replace(' ', '-', $_POST['slug'] ?? '')))),
        'title'            => trim(strip_tags($_POST['title']            ?? '')),
        'excerpt'          => trim(strip_tags($_POST['excerpt']          ?? '')),
        'content'          => trim($_POST['content']                     ?? ''), // HTML allowed
        'category'         => trim(strip_tags($_POST['category']         ?? '')),
        'featured_image'   => $featured_image_upload ?: trim($_POST['featured_image'] ?? ''),
        'meta_title'       => trim(strip_tags($_POST['meta_title']       ?? '')),
        'meta_description' => trim(strip_tags($_POST['meta_description'] ?? '')),
        'og_image'         => $og_image_upload ?: trim($_POST['og_image'] ?? ''),
        'author'           => trim(strip_tags($_POST['author']           ?? 'General Pest Removal Team')),
        'is_published'     => isset($_POST['is_published']) ? 1 : 0,
    ];

    if (empty($data['slug']) || empty($data['title'])) {
        $error = "Slug and Title are required.";
    } else {
        try {
            if ($is_edit && $post) {
                $sql = "UPDATE blog_posts SET slug=:slug, title=:title, excerpt=:excerpt, content=:content,
                        category=:category, featured_image=:featured_image, meta_title=:meta_title,
                        meta_description=:meta_description, og_image=:og_image, author=:author,
                        is_published=:is_published WHERE id=:id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':slug'             => $data['slug'],
                    ':title'            => $data['title'],
                    ':excerpt'          => $data['excerpt'],
                    ':content'          => $data['content'],
                    ':category'         => $data['category'],
                    ':featured_image'   => $data['featured_image'],
                    ':meta_title'       => $data['meta_title'],
                    ':meta_description' => $data['meta_description'],
                    ':og_image'         => $data['og_image'],
                    ':author'           => $data['author'],
                    ':is_published'     => $data['is_published'],
                    ':id'               => $post['id'],
                ]);
                $success = "Post updated successfully.";
                // Reload post
                $stmt2 = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ? LIMIT 1");
                $stmt2->execute([$post['id']]);
                $post = $stmt2->fetch(PDO::FETCH_ASSOC);
            } else {
                $stmt = $pdo->prepare(
                    "INSERT INTO blog_posts (slug, title, excerpt, content, category, featured_image, meta_title, meta_description, og_image, author, is_published)
                     VALUES (:slug, :title, :excerpt, :content, :category, :featured_image, :meta_title, :meta_description, :og_image, :author, :is_published)"
                );
                $stmt->execute([
                    ':slug'             => $data['slug'],
                    ':title'            => $data['title'],
                    ':excerpt'          => $data['excerpt'],
                    ':content'          => $data['content'],
                    ':category'         => $data['category'],
                    ':featured_image'   => $data['featured_image'],
                    ':meta_title'       => $data['meta_title'],
                    ':meta_description' => $data['meta_description'],
                    ':og_image'         => $data['og_image'],
                    ':author'           => $data['author'],
                    ':is_published'     => $data['is_published'],
                ]);
                $new_id = $pdo->lastInsertId();
                header("Location: /general-pest-removal/admin/blogs/edit?id={$new_id}&saved=1");
                exit;
            }
        } catch (PDOException $e) {
            $error = "Save failed: " . $e->getMessage();
        }
    }
}

if (isset($_GET['saved'])) {
    $success = "Post created successfully.";
}

$admin_title = $is_edit ? 'Edit Post' : 'New Post';

require_once BASE_DIR . '/admin/head.php';
require_once BASE_DIR . '/admin/sidebar.php';
?>

<!-- Main -->
<main class="flex-grow p-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <a href="/general-pest-removal/admin/blogs" class="text-gray-400 hover:text-primary transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900"><?= $is_edit ? 'Edit Post' : 'New Blog Post' ?></h1>
            <?php if ($is_edit && $post): ?>
            <a href="/general-pest-removal/blog/<?= htmlspecialchars($post['slug']) ?>" target="_blank"
               class="ml-auto text-xs text-gray-400 hover:text-primary transition flex items-center gap-1">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> View Live
            </a>
            <?php endif; ?>
        </div>

        <?php if ($success): ?>
        <div class="bg-yellow-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            <i class="fa-solid fa-check-circle"></i> <?= htmlspecialchars($success) ?>
        </div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <div class="bg-white rounded-xl shadow-sm p-6 space-y-5">
                <h2 class="font-bold text-gray-700 text-sm uppercase tracking-wide border-b pb-3">Content</h2>

                <!-- Title -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Post Title *</label>
                    <input type="text" name="title" id="post-title" required
                           value="<?= htmlspecialchars($post['title'] ?? '') ?>"
                           placeholder="e.g., 5 Signs You Have Termites in Your Sydney Home"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm transition">
                </div>

                <!-- Slug -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">URL Slug *</label>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-400 text-sm">/blog/</span>
                        <input type="text" name="slug" id="post-slug" required
                               value="<?= htmlspecialchars($post['slug'] ?? '') ?>"
                               placeholder="signs-bed-bugs-toronto-apartment"
                               class="flex-grow px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm font-mono transition">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Lowercase letters, numbers, and hyphens only. Auto-generated from title.</p>
                </div>

                <!-- Excerpt -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Excerpt / Summary</label>
                    <textarea name="excerpt" rows="3"
                              placeholder="1–2 sentence summary shown on the blog listing page..."
                              class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm transition"><?= htmlspecialchars($post['excerpt'] ?? '') ?></textarea>
                </div>

                <!-- Content -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Article Content (HTML allowed)</label>
                    <textarea name="content" rows="20"
                              placeholder="<h2>Section Title</h2><p>Your content here...</p>"
                              class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm font-mono transition"><?= htmlspecialchars($post['content'] ?? '') ?></textarea>
                    <p class="text-xs text-gray-400 mt-1">Use &lt;h2&gt;, &lt;h3&gt;, &lt;p&gt;, &lt;strong&gt;, &lt;a href="..."&gt; tags. Include internal links to /services, /booking, /faq for best SEO.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Category</label>
                        <select name="category" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none bg-white text-sm transition">
                            <?php foreach (['Bed Bugs','Rodent Control','Cockroaches','Commercial','Eco-Friendly','General'] as $cat): ?>
                            <option value="<?= $cat ?>" <?= ($post['category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Author</label>
                        <input type="text" name="author"
                               value="<?= htmlspecialchars($post['author'] ?? 'General Pest Removal Team') ?>"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm transition">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Featured Image</label>
                        <?php $fi_val = htmlspecialchars($post['featured_image'] ?? ''); ?>
                        <!-- Preview -->
                        <div id="fi-preview-wrap" class="<?= $fi_val ? '' : 'hidden' ?> mb-2 relative w-40 h-28 rounded-lg overflow-hidden border border-gray-200 bg-gray-50">
                            <img id="fi-preview" src="<?= $fi_val ?>" alt="Featured image preview" class="w-full h-full object-cover">
                            <button type="button" onclick="clearImage('featured_image','fi-preview-wrap','fi-preview','fi-file')"
                                    class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full text-xs flex items-center justify-center hover:bg-red-700">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>
                        <!-- File upload -->
                        <label class="flex items-center gap-2 cursor-pointer w-fit bg-gray-100 hover:bg-gray-200 border border-dashed border-gray-300 px-4 py-2.5 rounded-lg transition mb-2">
                            <i class="fa-solid fa-upload text-gray-500 text-sm"></i>
                            <span class="text-sm text-gray-600 font-medium">Upload image</span>
                            <input type="file" name="featured_image_file" id="fi-file" accept="image/*"
                                   class="hidden" onchange="previewImage(this,'fi-preview-wrap','fi-preview')">
                        </label>
                        <p class="text-xs text-gray-400 mb-1">Or enter a URL manually:</p>
                        <input type="text" name="featured_image" id="featured_image"
                               value="<?= $fi_val ?>"
                               placeholder="/assets/images/post.jpg"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm transition"
                               oninput="updatePreview(this.value,'fi-preview-wrap','fi-preview')">
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP · Max 5 MB</p>
                    </div>
                </div>
            </div>

            <!-- SEO Section -->
            <div class="bg-white rounded-xl shadow-sm p-6 space-y-5">
                <h2 class="font-bold text-gray-700 text-sm uppercase tracking-wide border-b pb-3">SEO Settings</h2>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Meta Title <span class="text-gray-400 font-normal" id="meta-title-count">(<?= strlen($post['meta_title'] ?? '') ?>/60)</span>
                    </label>
                    <input type="text" name="meta_title" maxlength="70"
                           value="<?= htmlspecialchars($post['meta_title'] ?? '') ?>"
                           placeholder="Leave blank to auto-generate from post title"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm transition"
                           oninput="document.getElementById('meta-title-count').textContent='('+this.value.length+'/60)'">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Meta Description <span class="text-gray-400 font-normal" id="meta-desc-count">(<?= strlen($post['meta_description'] ?? '') ?>/160)</span>
                    </label>
                    <textarea name="meta_description" rows="3" maxlength="180"
                              placeholder="Leave blank to auto-generate from excerpt"
                              class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm transition"
                              oninput="document.getElementById('meta-desc-count').textContent='('+this.value.length+'/160)'"><?= htmlspecialchars($post['meta_description'] ?? '') ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">OG Image <span class="text-gray-400 font-normal">(1200×630px recommended)</span></label>
                    <?php $og_val = htmlspecialchars($post['og_image'] ?? ''); ?>
                    <div id="og-preview-wrap" class="<?= $og_val ? '' : 'hidden' ?> mb-2 relative w-40 h-24 rounded-lg overflow-hidden border border-gray-200 bg-gray-50">
                        <img id="og-preview" src="<?= $og_val ?>" alt="OG image preview" class="w-full h-full object-cover">
                        <button type="button" onclick="clearImage('og_image','og-preview-wrap','og-preview','og-file')"
                                class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full text-xs flex items-center justify-center hover:bg-red-700">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer w-fit bg-gray-100 hover:bg-gray-200 border border-dashed border-gray-300 px-4 py-2.5 rounded-lg transition mb-2">
                        <i class="fa-solid fa-upload text-gray-500 text-sm"></i>
                        <span class="text-sm text-gray-600 font-medium">Upload OG image</span>
                        <input type="file" name="og_image_file" id="og-file" accept="image/*"
                               class="hidden" onchange="previewImage(this,'og-preview-wrap','og-preview')">
                    </label>
                    <p class="text-xs text-gray-400 mb-1">Or enter a URL manually (leave blank to use featured image):</p>
                    <input type="text" name="og_image"
                           value="<?= $og_val ?>"
                           placeholder="Leave blank to use featured image"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm transition"
                           oninput="updatePreview(this.value,'og-preview-wrap','og-preview')">
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="is_published" name="is_published" value="1"
                           <?= !$is_edit || !empty($post['is_published']) ? 'checked' : '' ?>
                           class="w-4 h-4">
                    <label for="is_published" class="text-sm font-bold text-gray-700 cursor-pointer">
                        Published <span class="text-gray-400 font-normal">(uncheck to save as draft)</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-between">
                <a href="/general-pest-removal/admin/blogs" class="text-gray-400 hover:text-gray-600 py-2.5 transition">← Back to Posts</a>
                <button type="submit" class="bg-primary hover:bg-blue-900 text-white font-bold px-10 py-2.5 rounded-lg transition">
                    <?= $is_edit ? 'Save Changes' : 'Create Post' ?>
                </button>
            </div>
        </form>
    </div>
</main>

<script>
// Image upload helpers
function previewImage(input, wrapId, imgId) {
    var file = input.files[0];
    if (!file) return;
    var wrap = document.getElementById(wrapId);
    var img  = document.getElementById(imgId);
    var reader = new FileReader();
    reader.onload = function (e) {
        img.src = e.target.result;
        wrap.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}

function updatePreview(val, wrapId, imgId) {
    var wrap = document.getElementById(wrapId);
    var img  = document.getElementById(imgId);
    if (val) { img.src = val; wrap.classList.remove('hidden'); }
    else     { wrap.classList.add('hidden'); }
}

function clearImage(textFieldId, wrapId, imgId, fileInputId) {
    document.getElementById(wrapId).classList.add('hidden');
    document.getElementById(imgId).src = '';
    document.getElementById(textFieldId).value = '';
    document.getElementById(fileInputId).value = '';
}

// Auto-generate slug from title
document.getElementById('post-title').addEventListener('input', function () {
    var slugField = document.getElementById('post-slug');
    if (!slugField.dataset.manuallyEdited) {
        slugField.value = this.value
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }
});
document.getElementById('post-slug').addEventListener('input', function () {
    this.dataset.manuallyEdited = '1';
});
</script>
</body>
</html>
