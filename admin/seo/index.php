<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_DIR')) define('BASE_DIR', dirname(__DIR__, 2));
require_once BASE_DIR . '/admin/auth.php';
require_once BASE_DIR . '/includes/db.php';
require_once BASE_DIR . '/includes/seo-meta.php';

$active_page = 'seo';
$admin_title = 'SEO Settings';

if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error   = $_SESSION['flash_error']   ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// All managed public pages
$managed_pages = [
    'home'     => ['label' => 'Home Page',    'url' => '/'],
    'about'    => ['label' => 'About Us',      'url' => '/about'],
    'services' => ['label' => 'Services',      'url' => '/services'],
    'booking'  => ['label' => 'Book Online',   'url' => '/booking'],
    'blogs'    => ['label' => 'Blog Listing',  'url' => '/blogs'],
    'faq'      => ['label' => 'FAQ Page',      'url' => '/faq'],
    'contact'  => ['label' => 'Contact Us',    'url' => '/contact'],
];

// ── Handle: clear overrides ────────────────────────────────────
if (isset($_GET['clear']) && isset($_GET['edit']) && $pdo) {
    $page_key = preg_replace('/[^a-z0-9_-]/', '', $_GET['edit']);
    if (isset($managed_pages[$page_key])) {
        try {
            $pdo->prepare("DELETE FROM seo_settings WHERE page_key = ?")->execute([$page_key]);
            $_SESSION['flash_success'] = "SEO overrides cleared for <strong>{$managed_pages[$page_key]['label']}</strong>. Page now uses built-in defaults.";
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'Could not clear overrides: ' . htmlspecialchars($e->getMessage());
        }
    }
    header('Location: /admin/seo?edit=' . $page_key);
    exit;
}

// ── Handle: save ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['page_key'])) {
    if (($_POST['csrf_token'] ?? '') !== $_SESSION['csrf_token']) {
        $_SESSION['flash_error'] = 'Invalid request.';
        header('Location: /admin/seo'); exit;
    }
    $page_key = preg_replace('/[^a-z0-9_-]/', '', $_POST['page_key']);
    if (isset($managed_pages[$page_key])) {
        $data = [
            'meta_title'       => trim(strip_tags($_POST['meta_title']       ?? '')),
            'meta_description' => trim(strip_tags($_POST['meta_description'] ?? '')),
            'og_title'         => trim(strip_tags($_POST['og_title']         ?? '')),
            'og_description'   => trim(strip_tags($_POST['og_description']   ?? '')),
            'og_image'         => trim($_POST['og_image']                    ?? ''),
            'canonical_url'    => trim($_POST['canonical_url']               ?? ''),
            'noindex'          => isset($_POST['noindex']) ? 1 : 0,
        ];
        if (save_page_seo($page_key, $managed_pages[$page_key]['label'], $data)) {
            $_SESSION['flash_success'] = "SEO settings saved for <strong>{$managed_pages[$page_key]['label']}</strong>.";
        } else {
            $_SESSION['flash_error'] = 'Failed to save. Check database connection.';
        }
        header('Location: /admin/seo?edit=' . $page_key);
        exit;
    }
}

// ── Load current DB values ─────────────────────────────────────
$current = [];
if ($pdo) {
    try {
        foreach ($pdo->query("SELECT * FROM seo_settings")->fetchAll() as $row) {
            $current[$row['page_key']] = $row;
        }
    } catch (PDOException $e) { /* ignore */ }
}

$editing   = preg_replace('/[^a-z0-9_-]/', '', $_GET['edit'] ?? '');
if (!isset($managed_pages[$editing])) $editing = array_key_first($managed_pages);
$edit_data = $current[$editing] ?? [];

require_once BASE_DIR . '/admin/head.php';
require_once BASE_DIR . '/admin/sidebar.php';
?>

<main class="flex-grow p-8 overflow-auto">
<div class="max-w-5xl mx-auto">

    <h1 class="text-2xl font-bold text-gray-900 mb-1">SEO Settings</h1>
    <p class="text-gray-500 text-sm mb-6">Override meta title, description, and Open Graph tags per page. Leave blank to use each page's built-in defaults.</p>

    <?php if ($flash_success): ?>
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
        <i class="fa-solid fa-check-circle" aria-hidden="true"></i> <?= $flash_success ?>
    </div>
    <?php endif; ?>
    <?php if ($flash_error): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> <?= htmlspecialchars($flash_error) ?>
    </div>
    <?php endif; ?>

    <div class="flex gap-6">

        <!-- Page Tabs -->
        <nav class="w-44 flex-shrink-0 space-y-1" aria-label="Pages">
            <?php foreach ($managed_pages as $key => $info): ?>
            <a href="?edit=<?= $key ?>"
               class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition
                      <?= $key === $editing ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-200' ?>">
                <span class="truncate"><?= htmlspecialchars($info['label']) ?></span>
                <?php if (isset($current[$key])): ?>
                <span class="w-2 h-2 rounded-full flex-shrink-0 <?= $key === $editing ? 'bg-green-300' : 'bg-secondary' ?>" title="Custom SEO active"></span>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
            <div class="pt-3 mt-1 border-t border-gray-200 px-1">
                <p class="text-xs text-gray-400 leading-relaxed flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-secondary flex-shrink-0 inline-block"></span>
                    Custom SEO saved
                </p>
            </div>
        </nav>

        <!-- Editor -->
        <div class="flex-grow bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900"><?= htmlspecialchars($managed_pages[$editing]['label']) ?></h2>
                <a href="/general-pest-removal<?= $managed_pages[$editing]['url'] ?>" target="_blank"
                   class="text-xs text-gray-400 hover:text-primary transition flex items-center gap-1">
                    <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i> View Live
                </a>
            </div>

            <form method="POST" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="page_key" value="<?= htmlspecialchars($editing) ?>">

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Meta Title
                        <span class="text-gray-400 font-normal ml-1" id="title-count"><?= strlen($edit_data['meta_title'] ?? '') ?>/60 chars</span>
                    </label>
                    <input type="text" name="meta_title" maxlength="70"
                           value="<?= htmlspecialchars($edit_data['meta_title'] ?? '') ?>"
                           placeholder="Leave blank to use page default"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm transition"
                           oninput="document.getElementById('title-count').textContent=this.value.length+'/60 chars'">
                    <p class="text-xs text-gray-400 mt-1">Recommended: 50–60 characters. Shown in Google results.</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Meta Description
                        <span class="text-gray-400 font-normal ml-1" id="desc-count"><?= strlen($edit_data['meta_description'] ?? '') ?>/160 chars</span>
                    </label>
                    <textarea name="meta_description" rows="3" maxlength="180"
                              placeholder="Leave blank to use page default"
                              class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm transition"
                              oninput="document.getElementById('desc-count').textContent=this.value.length+'/160 chars'"><?= htmlspecialchars($edit_data['meta_description'] ?? '') ?></textarea>
                    <p class="text-xs text-gray-400 mt-1">Recommended: 120–160 characters. Shown below the title in Google.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">OG Title <span class="text-gray-400 font-normal">(Social Share)</span></label>
                        <input type="text" name="og_title"
                               value="<?= htmlspecialchars($edit_data['og_title'] ?? '') ?>"
                               placeholder="Defaults to Meta Title"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">OG Image URL <span class="text-gray-400 font-normal">(1200×630px)</span></label>
                        <input type="url" name="og_image"
                               value="<?= htmlspecialchars($edit_data['og_image'] ?? '') ?>"
                               placeholder="https://..."
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">OG Description <span class="text-gray-400 font-normal">(Social Share)</span></label>
                    <textarea name="og_description" rows="2"
                              placeholder="Defaults to Meta Description"
                              class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm transition"><?= htmlspecialchars($edit_data['og_description'] ?? '') ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Canonical URL <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input type="url" name="canonical_url"
                           value="<?= htmlspecialchars($edit_data['canonical_url'] ?? '') ?>"
                           placeholder="https://yourdomain.com<?= $managed_pages[$editing]['url'] ?>"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none text-sm transition">
                </div>

                <div class="flex items-center gap-3 p-4 bg-red-50 rounded-lg">
                    <input type="checkbox" id="noindex" name="noindex" value="1"
                           <?= !empty($edit_data['noindex']) ? 'checked' : '' ?> class="w-4 h-4">
                    <label for="noindex" class="text-sm font-bold text-red-800 cursor-pointer">
                        No-Index this page <span class="font-normal text-red-600">(hides from Google — use only for admin/utility pages)</span>
                    </label>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <?php if (isset($current[$editing])): ?>
                    <a href="?edit=<?= $editing ?>&clear=1"
                       onclick="return confirm('Remove all custom SEO overrides for this page and revert to defaults?')"
                       class="text-xs text-red-400 hover:text-red-700 transition flex items-center gap-1">
                        <i class="fa-solid fa-rotate-left" aria-hidden="true"></i> Revert to defaults
                    </a>
                    <?php else: ?>
                    <span class="text-xs text-gray-400 italic">Using page defaults (no DB overrides)</span>
                    <?php endif; ?>
                    <button type="submit" class="bg-primary hover:bg-blue-900 text-white font-bold px-8 py-2.5 rounded-lg transition">
                        Save SEO Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Overview Table -->
    <div class="mt-8 bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-900">All Pages Overview</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Page</th>
                    <th class="px-4 py-3 text-left">Meta Title</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($managed_pages as $key => $info): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">
                        <?= htmlspecialchars($info['label']) ?>
                        <span class="text-gray-400 font-normal text-xs ml-1"><?= $info['url'] ?></span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs max-w-xs truncate">
                        <?= !empty($current[$key]['meta_title'])
                            ? htmlspecialchars($current[$key]['meta_title'])
                            : '<span class="italic text-gray-300">Using page default</span>' ?>
                    </td>
                    <td class="px-4 py-3">
                        <?php if (isset($current[$key])): ?>
                        <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full">Custom</span>
                        <?php else: ?>
                        <span class="bg-gray-100 text-gray-400 text-xs font-bold px-2 py-0.5 rounded-full">Default</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                        <a href="?edit=<?= $key ?>" class="text-xs text-primary hover:underline font-bold">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
</main>

</body>
</html>
