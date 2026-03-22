<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_DIR')) define('BASE_DIR', dirname(__DIR__, 2));
require_once BASE_DIR . '/admin/auth.php';
require_once BASE_DIR . '/includes/db.php';

$active_page = 'blogs';
$admin_title = 'Blog Posts';

if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error   = $_SESSION['flash_error']   ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// POST: toggle publish or delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $pdo) {
    if (($_POST['csrf_token'] ?? '') !== $_SESSION['csrf_token']) {
        $_SESSION['flash_error'] = 'Invalid request.';
        header('Location: /general-pest-removal/admin/blogs'); exit;
    }
    $id = (int)($_POST['id'] ?? 0);
    if ($_POST['action'] === 'toggle' && $id) {
        try {
            $pdo->prepare("UPDATE blog_posts SET is_published = 1 - is_published WHERE id = ?")->execute([$id]);
            $_SESSION['flash_success'] = 'Post status updated.';
        } catch (PDOException $e) { $_SESSION['flash_error'] = 'Update failed.'; }
    } elseif ($_POST['action'] === 'delete' && $id) {
        try {
            $pdo->prepare("DELETE FROM blog_posts WHERE id = ?")->execute([$id]);
            $_SESSION['flash_success'] = 'Post deleted.';
        } catch (PDOException $e) { $_SESSION['flash_error'] = 'Delete failed.'; }
    }
    header('Location: /general-pest-removal/admin/blogs'); exit;
}

$posts = [];
if ($pdo) {
    try {
        $posts = $pdo->query("SELECT id, slug, title, category, author, is_published, published_at, created_at FROM blog_posts ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) { $flash_error = 'Could not load posts.'; }
}

require_once BASE_DIR . '/admin/head.php';
require_once BASE_DIR . '/admin/sidebar.php';
?>
<main class="flex-grow p-8 overflow-auto">
<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Blog Posts</h1>
        <a href="/general-pest-removal/admin/blogs/edit"
           class="bg-accent hover:bg-orange-700 text-white font-bold px-5 py-2.5 rounded-lg transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> New Post
        </a>
    </div>

    <?php if ($flash_success): ?>
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
        <i class="fa-solid fa-check-circle"></i> <?= htmlspecialchars($flash_success) ?>
    </div>
    <?php endif; ?>
    <?php if ($flash_error): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($flash_error) ?>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <?php if (empty($posts)): ?>
        <div class="px-6 py-12 text-center text-gray-400">
            <i class="fa-solid fa-newspaper text-4xl mb-3 block"></i>
            <p class="mb-4">No blog posts yet.</p>
            <a href="/general-pest-removal/admin/blogs/edit" class="bg-primary text-white px-5 py-2 rounded-lg font-bold hover:bg-blue-900 transition">Create First Post</a>
        </div>
        <?php else: ?>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Title</th>
                    <th class="px-4 py-3 text-left">Category</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($posts as $p): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-900 line-clamp-1"><?= htmlspecialchars($p['title']) ?></p>
                        <p class="text-xs text-gray-400 font-mono mt-0.5">/blog/<?= htmlspecialchars($p['slug']) ?></p>
                    </td>
                    <td class="px-4 py-3 text-gray-600 text-xs"><?= htmlspecialchars($p['category']) ?></td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold <?= $p['is_published'] ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500' ?>">
                            <?= $p['is_published'] ? 'Published' : 'Draft' ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs whitespace-nowrap"><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2 flex-wrap">
                            <a href="/general-pest-removal/admin/blogs/edit?id=<?= $p['id'] ?>" class="text-primary hover:underline text-xs font-bold">Edit</a>
                            <a href="/general-pest-removal/blog/<?= htmlspecialchars($p['slug']) ?>" target="_blank" class="text-gray-400 hover:text-primary text-xs">View</a>
                            <form method="POST" class="inline">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button type="submit" class="text-xs <?= $p['is_published'] ? 'text-orange-500' : 'text-secondary' ?> hover:underline cursor-pointer">
                                    <?= $p['is_published'] ? 'Unpublish' : 'Publish' ?>
                                </button>
                            </form>
                            <form method="POST" class="inline" onsubmit="return confirm('Delete this post permanently?')">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button type="submit" class="text-xs text-red-500 hover:underline cursor-pointer">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
</main>
</body>
</html>
