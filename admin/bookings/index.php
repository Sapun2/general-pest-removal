<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_DIR')) define('BASE_DIR', dirname(__DIR__, 2));
require_once BASE_DIR . '/admin/auth.php';
require_once BASE_DIR . '/includes/db.php';

$active_page = 'bookings';
$admin_title = 'Bookings';

if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error   = $_SESSION['flash_error']   ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$valid_statuses = ['new', 'contacted', 'completed', 'cancelled'];

// ── Update status or delete ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id']) && $pdo) {
    if (($_POST['csrf_token'] ?? '') !== $_SESSION['csrf_token']) {
        $_SESSION['flash_error'] = 'Invalid request.';
        header('Location: /general-pest-removal/admin/bookings'); exit;
    }
    $id     = (int)$_POST['id'];
    $action = $_POST['action'];

    if ($action === 'status' && isset($_POST['status']) && in_array($_POST['status'], $valid_statuses)) {
        try {
            $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?")
                ->execute([$_POST['status'], $id]);
            $_SESSION['flash_success'] = 'Booking status updated.';
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'Update failed.';
        }
        $view_redirect = isset($_POST['view_id']) ? '?view=' . (int)$_POST['view_id'] : '';
        header('Location: /general-pest-removal/admin/bookings' . $view_redirect);
        exit;
    }

    if ($action === 'delete') {
        try {
            $pdo->prepare("DELETE FROM bookings WHERE id = ?")->execute([$id]);
            $_SESSION['flash_success'] = 'Booking deleted.';
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'Delete failed.';
        }
        header('Location: /general-pest-removal/admin/bookings');
        exit;
    }
}

// ── Filters ─────────────────────────────────────────────────────
$filter_status = in_array($_GET['status'] ?? '', $valid_statuses) ? $_GET['status'] : '';
$filter_pest   = preg_replace('/[^a-z0-9\s-]/i', '', $_GET['pest'] ?? '');
$search        = trim($_GET['q'] ?? '');

// ── Single booking view ─────────────────────────────────────────
$viewing = null;
if (isset($_GET['view']) && $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$_GET['view']]);
        $viewing = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
}

// ── List bookings ───────────────────────────────────────────────
$bookings = [];
$total    = 0;
$per_page = 20;
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
        if ($filter_pest) {
            $where[]  = 'pest_type LIKE ?';
            $params[] = '%' . $filter_pest . '%';
        }
        if ($search) {
            $where[]  = '(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ? OR city LIKE ?)';
            $s = '%' . $search . '%';
            array_push($params, $s, $s, $s, $s, $s);
        }

        $sql_where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $total_stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings $sql_where");
        $total_stmt->execute($params);
        $total = (int)$total_stmt->fetchColumn();

        $stmt = $pdo->prepare(
            "SELECT id, first_name, last_name, email, phone, city, pest_type, status, created_at
             FROM bookings $sql_where ORDER BY created_at DESC LIMIT $per_page OFFSET $offset"
        );
        $stmt->execute($params);
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $flash_error = 'Could not load bookings.';
    }
}

$total_pages = (int)ceil($total / $per_page);

require_once BASE_DIR . '/admin/head.php';
require_once BASE_DIR . '/admin/sidebar.php';

$status_classes = [
    'new'       => 'bg-accent text-white',
    'contacted' => 'bg-blue-100 text-blue-700',
    'completed' => 'bg-green-100 text-green-700',
    'cancelled' => 'bg-gray-200 text-gray-500',
];
?>

<main class="flex-grow p-8 overflow-auto">
<div class="max-w-6xl mx-auto">

    <h1 class="text-2xl font-bold text-gray-900 mb-6">Bookings</h1>

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
    <!-- ── Single Booking Detail ─────────────────────────────── -->
    <div class="mb-6">
        <a href="/general-pest-removal/admin/bookings" class="text-sm text-gray-400 hover:text-primary flex items-center gap-1 mb-4 w-fit">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back to All Bookings
        </a>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-start justify-between mb-6 flex-wrap gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">
                        <?= htmlspecialchars($viewing['first_name'] . ' ' . $viewing['last_name']) ?>
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5">Submitted <?= date('F j, Y \a\t g:i a', strtotime($viewing['created_at'])) ?></p>
                </div>
                <span class="text-sm font-bold px-3 py-1.5 rounded-full <?= $status_classes[$viewing['status']] ?? 'bg-gray-200 text-gray-500' ?>">
                    <?= htmlspecialchars(ucfirst($viewing['status'])) ?>
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="space-y-3">
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Contact</p>
                        <p class="font-medium"><?= htmlspecialchars($viewing['email']) ?></p>
                        <p class="font-medium"><?= htmlspecialchars($viewing['phone']) ?></p>
                    </div>
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Location</p>
                        <p class="font-medium"><?= htmlspecialchars($viewing['city'] ?? '—') ?></p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Pest Type</p>
                        <p class="font-medium capitalize"><?= htmlspecialchars($viewing['pest_type'] ?? '—') ?></p>
                    </div>
                    <?php if (!empty($viewing['address'])): ?>
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Address</p>
                        <p class="font-medium"><?= htmlspecialchars($viewing['address']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($viewing['message'])): ?>
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Notes</p>
                        <p class="text-sm text-gray-700"><?= nl2br(htmlspecialchars($viewing['message'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Status update + Delete -->
            <div class="flex items-center gap-4 pt-4 border-t border-gray-100 flex-wrap">
                <form method="POST" class="flex items-center gap-3">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="id" value="<?= $viewing['id'] ?>">
                    <input type="hidden" name="action" value="status">
                    <input type="hidden" name="view_id" value="<?= $viewing['id'] ?>">
                    <select name="status" class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-primary outline-none">
                        <?php foreach ($valid_statuses as $s): ?>
                        <option value="<?= $s ?>" <?= $viewing['status'] === $s ? 'selected' : '' ?>>
                            <?= ucfirst($s) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="bg-primary hover:bg-blue-900 text-white font-bold px-5 py-2 rounded-lg text-sm transition">
                        Update Status
                    </button>
                </form>
                <a href="mailto:<?= htmlspecialchars($viewing['email']) ?>"
                   class="text-sm text-primary hover:underline flex items-center gap-1">
                    <i class="fa-solid fa-envelope" aria-hidden="true"></i> Email Customer
                </a>
                <form method="POST" class="ml-auto"
                      onsubmit="return confirm('Delete this booking permanently?')">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="id" value="<?= $viewing['id'] ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="text-sm text-red-500 hover:text-red-700 transition">
                        <i class="fa-solid fa-trash" aria-hidden="true"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- ── Booking List ──────────────────────────────────────── -->

    <!-- Filters -->
    <form method="GET" class="bg-white rounded-xl shadow-sm p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div class="flex-grow min-w-[180px]">
            <label class="block text-xs text-gray-500 mb-1">Search</label>
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
                   placeholder="Name, email, phone, city…"
                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-primary outline-none">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Status</label>
            <select name="status" class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-primary outline-none bg-white">
                <option value="">All Statuses</option>
                <?php foreach ($valid_statuses as $s): ?>
                <option value="<?= $s ?>" <?= $filter_status === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Pest Type</label>
            <input type="text" name="pest" value="<?= htmlspecialchars($filter_pest) ?>"
                   placeholder="e.g. bed bugs"
                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-primary outline-none">
        </div>
        <button type="submit" class="bg-primary text-white font-bold px-5 py-2 rounded-lg text-sm hover:bg-blue-900 transition">Filter</button>
        <?php if ($filter_status || $filter_pest || $search): ?>
        <a href="/general-pest-removal/admin/bookings" class="text-sm text-gray-400 hover:text-gray-700 py-2">Clear</a>
        <?php endif; ?>
    </form>

    <!-- Stats bar -->
    <div class="flex items-center justify-between mb-3">
        <p class="text-sm text-gray-500">
            <?= $total ?> booking<?= $total !== 1 ? 's' : '' ?>
            <?= $filter_status ? '· filtered by <strong>' . ucfirst($filter_status) . '</strong>' : '' ?>
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <?php if (empty($bookings)): ?>
        <div class="px-6 py-12 text-center text-gray-400">
            <i class="fa-solid fa-calendar-xmark text-4xl mb-3 block" aria-hidden="true"></i>
            <p>No bookings found.</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">Customer</th>
                    <th class="px-4 py-3 text-left">Pest / Address</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Submitted</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($bookings as $b): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-400 text-xs"><?= $b['id'] ?></td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-900"><?= htmlspecialchars($b['first_name'] . ' ' . $b['last_name']) ?></p>
                        <p class="text-xs text-gray-400"><?= htmlspecialchars($b['email']) ?></p>
                        <p class="text-xs text-gray-400"><?= htmlspecialchars($b['phone']) ?></p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="capitalize text-gray-700"><?= htmlspecialchars($b['pest_type']) ?></p>
                        <p class="text-xs text-gray-400"><?= htmlspecialchars($b['city']) ?></p>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full <?= $status_classes[$b['status']] ?? 'bg-gray-200 text-gray-500' ?>">
                            <?= ucfirst(htmlspecialchars($b['status'])) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs whitespace-nowrap">
                        <?= date('M j, g:ia', strtotime($b['created_at'])) ?>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <a href="?view=<?= $b['id'] ?>" class="text-primary hover:underline text-xs font-bold">View</a>
                            <form method="POST" class="inline" onsubmit="return confirm('Delete this booking?')">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="text-xs text-red-400 hover:text-red-700 transition">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="px-4 py-4 border-t border-gray-100 flex items-center justify-between text-sm">
            <p class="text-gray-400">Page <?= $page_num ?> of <?= $total_pages ?></p>
            <div class="flex gap-2">
                <?php
                $qs = http_build_query(array_filter(['status' => $filter_status, 'pest' => $filter_pest, 'q' => $search]));
                if ($page_num > 1): ?>
                <a href="?p=<?= $page_num - 1 ?>&<?= $qs ?>" class="px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 transition text-gray-600">
                    &larr; Prev
                </a>
                <?php endif; ?>
                <?php if ($page_num < $total_pages): ?>
                <a href="?p=<?= $page_num + 1 ?>&<?= $qs ?>" class="px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 transition text-gray-600">
                    Next &rarr;
                </a>
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
