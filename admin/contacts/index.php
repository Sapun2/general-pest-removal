<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_DIR')) define('BASE_DIR', dirname(__DIR__, 2));
require_once BASE_DIR . '/admin/auth.php';
require_once BASE_DIR . '/includes/db.php';

$active_page = 'contacts';
$admin_title = 'Messages';

if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error   = $_SESSION['flash_error']   ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// ── POST actions ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id']) && $pdo) {
    if (($_POST['csrf_token'] ?? '') !== $_SESSION['csrf_token']) {
        $_SESSION['flash_error'] = 'Invalid request.';
        header('Location: /general-pest-removal/admin/contacts'); exit;
    }
    $id     = (int)$_POST['id'];
    $action = $_POST['action'];

    if ($action === 'mark_read') {
        try {
            $pdo->prepare("UPDATE contacts SET status = 'read' WHERE id = ?")->execute([$id]);
            $_SESSION['flash_success'] = 'Message marked as read.';
        } catch (PDOException $e) { $_SESSION['flash_error'] = 'Update failed.'; }
        header('Location: /general-pest-removal/admin/contacts?view=' . $id);
        exit;
    }

    if ($action === 'mark_unread') {
        try {
            $pdo->prepare("UPDATE contacts SET status = 'unread' WHERE id = ?")->execute([$id]);
            $_SESSION['flash_success'] = 'Message marked as unread.';
        } catch (PDOException $e) { $_SESSION['flash_error'] = 'Update failed.'; }
        header('Location: /general-pest-removal/admin/contacts');
        exit;
    }

    if ($action === 'delete') {
        try {
            $pdo->prepare("DELETE FROM contacts WHERE id = ?")->execute([$id]);
            $_SESSION['flash_success'] = 'Message deleted.';
        } catch (PDOException $e) { $_SESSION['flash_error'] = 'Delete failed.'; }
        header('Location: /general-pest-removal/admin/contacts');
        exit;
    }
}

// ── Single message view ─────────────────────────────────────────
$viewing = null;
if (isset($_GET['view']) && $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$_GET['view']]);
        $viewing = $stmt->fetch(PDO::FETCH_ASSOC);
        // Auto-mark as read when opened
        if ($viewing && $viewing['status'] === 'unread') {
            $pdo->prepare("UPDATE contacts SET status = 'read' WHERE id = ?")->execute([$viewing['id']]);
            $viewing['status'] = 'read';
        }
    } catch (PDOException $e) {}
}

// ── Filter ──────────────────────────────────────────────────────
$filter_status = in_array($_GET['status'] ?? '', ['unread', 'read']) ? $_GET['status'] : '';
$search        = trim($_GET['q'] ?? '');

// ── List messages ───────────────────────────────────────────────
$messages = [];
$total    = 0;
$per_page = 25;
$page_num = max(1, (int)($_GET['p'] ?? 1));
$offset   = ($page_num - 1) * $per_page;

if ($pdo) {
    try {
        $where  = [];
        $params = [];

        if ($filter_status) {
            $where[]  = 'status = ?';
            $params[] = $filter_status;
        }
        if ($search) {
            $where[]  = '(name LIKE ? OR email LIKE ? OR phone LIKE ? OR message LIKE ?)';
            $s = '%' . $search . '%';
            array_push($params, $s, $s, $s, $s);
        }

        $sql_where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $cnt = $pdo->prepare("SELECT COUNT(*) FROM contacts $sql_where");
        $cnt->execute($params);
        $total = (int)$cnt->fetchColumn();

        $stmt = $pdo->prepare(
            "SELECT id, name, email, phone, message, status, created_at
             FROM contacts $sql_where ORDER BY created_at DESC LIMIT $per_page OFFSET $offset"
        );
        $stmt->execute($params);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $flash_error = 'Could not load messages.';
    }
}

$total_pages = (int)ceil($total / $per_page);

require_once BASE_DIR . '/admin/head.php';
require_once BASE_DIR . '/admin/sidebar.php';
?>

<main class="flex-grow p-8 overflow-auto">
<div class="max-w-5xl mx-auto">

    <h1 class="text-2xl font-bold text-gray-900 mb-6">Messages</h1>

    <?php if ($flash_success): ?>
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
        <i class="fa-solid fa-check-circle" aria-hidden="true"></i> <?= htmlspecialchars($flash_success) ?>
    </div>
    <?php endif; ?>
    <?php if ($flash_error): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> <?= htmlspecialchars($flash_error) ?>
    </div>
    <?php endif; ?>

    <?php if ($viewing): ?>
    <!-- ── Single Message View ───────────────────────────────── -->
    <div class="mb-6">
        <a href="/general-pest-removal/admin/contacts" class="text-sm text-gray-400 hover:text-primary flex items-center gap-1 mb-4 w-fit">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back to All Messages
        </a>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-start justify-between mb-4 flex-wrap gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($viewing['name']) ?></h2>
                    <p class="text-sm text-gray-500 mt-0.5">Received <?= date('F j, Y \a\t g:i a', strtotime($viewing['created_at'])) ?></p>
                </div>
                <span class="text-xs font-bold px-3 py-1.5 rounded-full
                    <?= $viewing['status'] === 'unread' ? 'bg-secondary text-white' : 'bg-gray-100 text-gray-500' ?>">
                    <?= ucfirst(htmlspecialchars($viewing['status'])) ?>
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 text-sm">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Email</p>
                    <a href="mailto:<?= htmlspecialchars($viewing['email']) ?>"
                       class="text-primary hover:underline"><?= htmlspecialchars($viewing['email']) ?></a>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Phone</p>
                    <a href="tel:<?= htmlspecialchars($viewing['phone']) ?>"
                       class="text-primary hover:underline"><?= htmlspecialchars($viewing['phone'] ?: '—') ?></a>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-800 leading-relaxed mb-6">
                <?= nl2br(htmlspecialchars($viewing['message'])) ?>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-4 pt-4 border-t border-gray-100 flex-wrap">
                <a href="mailto:<?= htmlspecialchars($viewing['email']) ?>?subject=Re: Your enquiry&body=Hi <?= rawurlencode($viewing['name']) ?>,"
                   class="bg-primary hover:bg-blue-900 text-white font-bold px-5 py-2 rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-reply" aria-hidden="true"></i> Reply via Email
                </a>

                <?php if ($viewing['status'] === 'read'): ?>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="id" value="<?= $viewing['id'] ?>">
                    <input type="hidden" name="action" value="mark_unread">
                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-800 transition border border-gray-300 px-4 py-2 rounded-lg">
                        Mark as Unread
                    </button>
                </form>
                <?php endif; ?>

                <form method="POST" class="ml-auto" onsubmit="return confirm('Delete this message permanently?')">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="id" value="<?= $viewing['id'] ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="text-sm text-red-500 hover:text-red-700 transition flex items-center gap-1">
                        <i class="fa-solid fa-trash" aria-hidden="true"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- ── Message List ──────────────────────────────────────── -->

    <!-- Filters -->
    <form method="GET" class="bg-white rounded-xl shadow-sm p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div class="flex-grow min-w-[200px]">
            <label class="block text-xs text-gray-500 mb-1">Search</label>
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
                   placeholder="Name, email, message…"
                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-primary outline-none">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Status</label>
            <select name="status" class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-primary outline-none bg-white">
                <option value="">All Messages</option>
                <option value="unread" <?= $filter_status === 'unread' ? 'selected' : '' ?>>Unread</option>
                <option value="read"   <?= $filter_status === 'read'   ? 'selected' : '' ?>>Read</option>
            </select>
        </div>
        <button type="submit" class="bg-primary text-white font-bold px-5 py-2 rounded-lg text-sm hover:bg-blue-900 transition">Filter</button>
        <?php if ($filter_status || $search): ?>
        <a href="/general-pest-removal/admin/contacts" class="text-sm text-gray-400 hover:text-gray-700 py-2">Clear</a>
        <?php endif; ?>
    </form>

    <p class="text-sm text-gray-500 mb-3"><?= $total ?> message<?= $total !== 1 ? 's' : '' ?></p>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <?php if (empty($messages)): ?>
        <div class="px-6 py-12 text-center text-gray-400">
            <i class="fa-solid fa-envelope text-4xl mb-3 block" aria-hidden="true"></i>
            <p>No messages found.</p>
        </div>
        <?php else: ?>
        <ul class="divide-y divide-gray-100">
            <?php foreach ($messages as $m): ?>
            <?php $unread = $m['status'] === 'unread'; ?>
            <li class="hover:bg-gray-50 transition <?= $unread ? 'bg-blue-50/30' : '' ?>">
                <a href="?view=<?= $m['id'] ?>" class="flex items-start gap-4 px-5 py-4">
                    <div class="flex-shrink-0 mt-1">
                        <?php if ($unread): ?>
                        <span class="w-2.5 h-2.5 rounded-full bg-secondary inline-block" title="Unread"></span>
                        <?php else: ?>
                        <span class="w-2.5 h-2.5 rounded-full bg-gray-200 inline-block"></span>
                        <?php endif; ?>
                    </div>
                    <div class="min-w-0 flex-grow">
                        <div class="flex items-baseline justify-between gap-2 flex-wrap">
                            <p class="font-<?= $unread ? 'bold' : 'medium' ?> text-gray-900 truncate">
                                <?= htmlspecialchars($m['name']) ?>
                                <span class="font-normal text-gray-400 text-xs ml-1"><?= htmlspecialchars($m['email']) ?></span>
                            </p>
                            <p class="text-xs text-gray-400 whitespace-nowrap flex-shrink-0">
                                <?= date('M j, g:ia', strtotime($m['created_at'])) ?>
                            </p>
                        </div>
                        <p class="text-sm text-gray-500 mt-0.5 line-clamp-1">
                            <?= htmlspecialchars(mb_strimwidth($m['message'], 0, 120, '…')) ?>
                        </p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-gray-300 flex-shrink-0 mt-1" aria-hidden="true"></i>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="px-4 py-4 border-t border-gray-100 flex items-center justify-between text-sm">
            <p class="text-gray-400">Page <?= $page_num ?> of <?= $total_pages ?></p>
            <div class="flex gap-2">
                <?php
                $qs = http_build_query(array_filter(['status' => $filter_status, 'q' => $search]));
                if ($page_num > 1): ?>
                <a href="?p=<?= $page_num - 1 ?>&<?= $qs ?>"
                   class="px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 transition text-gray-600">&larr; Prev</a>
                <?php endif; ?>
                <?php if ($page_num < $total_pages): ?>
                <a href="?p=<?= $page_num + 1 ?>&<?= $qs ?>"
                   class="px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 transition text-gray-600">Next &rarr;</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>
</main>

</body>
</html>
