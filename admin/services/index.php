<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_DIR')) define('BASE_DIR', dirname(__DIR__, 2));
require_once BASE_DIR . '/admin/auth.php';
require_once BASE_DIR . '/includes/db.php';

$active_page = 'services';
$admin_title = 'Services';

if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error   = $_SESSION['flash_error']   ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    if (($_POST['csrf_token'] ?? '') !== $_SESSION['csrf_token']) {
        $_SESSION['flash_error'] = 'Invalid request.';
        header('Location: /admin/services'); exit;
    }
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle' && $id) {
        try {
            $pdo->prepare("UPDATE services SET is_active = 1 - is_active WHERE id = ?")->execute([$id]);
            $_SESSION['flash_success'] = 'Service status updated.';
        } catch (PDOException $e) { $_SESSION['flash_error'] = 'Update failed.'; }
    } elseif ($action === 'delete' && $id) {
        try {
            $pdo->prepare("DELETE FROM services WHERE id = ?")->execute([$id]);
            $_SESSION['flash_success'] = 'Service deleted.';
        } catch (PDOException $e) { $_SESSION['flash_error'] = 'Delete failed.'; }
    } elseif ($action === 'reorder_up' && $id) {
        try {
            $curr_stmt = $pdo->prepare("SELECT sort_order FROM services WHERE id = ?");
            $curr_stmt->execute([$id]);
            $curr_order = (int)$curr_stmt->fetchColumn();
            $prev = $pdo->prepare("SELECT id, sort_order FROM services WHERE sort_order < ? ORDER BY sort_order DESC LIMIT 1");
            $prev->execute([$curr_order]);
            $prev_row = $prev->fetch(PDO::FETCH_ASSOC);
            if ($prev_row) {
                $pdo->prepare("UPDATE services SET sort_order = ? WHERE id = ?")->execute([$prev_row['sort_order'], $id]);
                $pdo->prepare("UPDATE services SET sort_order = ? WHERE id = ?")->execute([$curr_order, $prev_row['id']]);
                $_SESSION['flash_success'] = 'Order updated.';
            }
        } catch (PDOException $e) { $_SESSION['flash_error'] = 'Reorder failed.'; }
    } elseif ($action === 'reorder_down' && $id) {
        try {
            $curr_stmt = $pdo->prepare("SELECT sort_order FROM services WHERE id = ?");
            $curr_stmt->execute([$id]);
            $curr_order = (int)$curr_stmt->fetchColumn();
            $next = $pdo->prepare("SELECT id, sort_order FROM services WHERE sort_order > ? ORDER BY sort_order ASC LIMIT 1");
            $next->execute([$curr_order]);
            $next_row = $next->fetch(PDO::FETCH_ASSOC);
            if ($next_row) {
                $pdo->prepare("UPDATE services SET sort_order = ? WHERE id = ?")->execute([$next_row['sort_order'], $id]);
                $pdo->prepare("UPDATE services SET sort_order = ? WHERE id = ?")->execute([$curr_order, $next_row['id']]);
                $_SESSION['flash_success'] = 'Order updated.';
            }
        } catch (PDOException $e) { $_SESSION['flash_error'] = 'Reorder failed.'; }
    }
    header('Location: /admin/services'); exit;
}

$services = [];
if ($pdo) {
    try {
        $services = $pdo->query("SELECT id, slug, name, tagline, icon, badge_text, is_active, sort_order FROM services ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) { $flash_error = 'Could not load services: ' . $e->getMessage(); }
}

require_once BASE_DIR . '/admin/head.php';
require_once BASE_DIR . '/admin/sidebar.php';
?>
<main class="flex-grow p-8 overflow-auto">
<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Services</h1>
            <p class="text-sm text-gray-400 mt-1">Manage the pest control services shown on your website.</p>
        </div>
        <a href="/admin/services/edit"
           class="bg-accent hover:bg-orange-700 text-white font-bold px-5 py-2.5 rounded-lg transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> New Service
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
        <?php if (empty($services)): ?>
        <div class="px-6 py-12 text-center text-gray-400">
            <i class="fa-solid fa-shield-bug text-4xl mb-3 block"></i>
            <p class="mb-4">No services yet.</p>
            <a href="/admin/services/edit" class="bg-primary text-white px-5 py-2 rounded-lg font-bold hover:bg-blue-900 transition">Add First Service</a>
        </div>
        <?php else: ?>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Order</th>
                    <th class="px-4 py-3 text-left">Service</th>
                    <th class="px-4 py-3 text-left">Slug</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($services as $svc): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1">
                            <span class="text-gray-500 font-mono text-xs w-6 text-center"><?= (int)$svc['sort_order'] ?></span>
                            <div class="flex flex-col gap-0.5">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <input type="hidden" name="action" value="reorder_up">
                                    <input type="hidden" name="id" value="<?= $svc['id'] ?>">
                                    <button type="submit" class="text-gray-300 hover:text-gray-600 leading-none p-0.5" title="Move up">
                                        <i class="fa-solid fa-chevron-up text-xs"></i>
                                    </button>
                                </form>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <input type="hidden" name="action" value="reorder_down">
                                    <input type="hidden" name="id" value="<?= $svc['id'] ?>">
                                    <button type="submit" class="text-gray-300 hover:text-gray-600 leading-none p-0.5" title="Move down">
                                        <i class="fa-solid fa-chevron-down text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <?php if ($svc['icon']): ?>
                            <div class="w-8 h-8 rounded-lg bg-accent/10 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid <?= htmlspecialchars($svc['icon']) ?> text-accent text-xs"></i>
                            </div>
                            <?php endif; ?>
                            <div>
                                <p class="font-medium text-gray-900"><?= htmlspecialchars($svc['name']) ?></p>
                                <?php if ($svc['tagline']): ?>
                                <p class="text-xs text-gray-400 mt-0.5"><?= htmlspecialchars($svc['tagline']) ?></p>
                                <?php endif; ?>
                                <?php if ($svc['badge_text']): ?>
                                <span class="inline-block mt-0.5 text-xs bg-secondary/10 text-secondary font-semibold px-1.5 py-0.5 rounded"><?= htmlspecialchars($svc['badge_text']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-400 font-mono text-xs">/services#<?= htmlspecialchars($svc['slug']) ?></td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold <?= $svc['is_active'] ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500' ?>">
                            <?= $svc['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2 flex-wrap">
                            <a href="/admin/services/edit?id=<?= $svc['id'] ?>" class="text-primary hover:underline text-xs font-bold">Edit</a>
                            <form method="POST" class="inline">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="id" value="<?= $svc['id'] ?>">
                                <button type="submit" class="text-xs <?= $svc['is_active'] ? 'text-orange-500' : 'text-secondary' ?> hover:underline cursor-pointer">
                                    <?= $svc['is_active'] ? 'Deactivate' : 'Activate' ?>
                                </button>
                            </form>
                            <form method="POST" class="inline" onsubmit="return confirm('Delete this service permanently?')">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $svc['id'] ?>">
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
