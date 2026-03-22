<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_DIR')) define('BASE_DIR', dirname(__DIR__));
require_once __DIR__ . '/auth.php';
require_once BASE_DIR . '/includes/db.php';

$active_page = 'dashboard';
$admin_title = 'Dashboard';

$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error   = $_SESSION['flash_error']   ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// ── Stats ──────────────────────────────────────────────────────────
$stats = [
    'bookings_new'     => 0,
    'bookings_total'   => 0,
    'bookings_today'   => 0,
    'bookings_week'    => 0,
    'contacts_unread'  => 0,
    'contacts_total'   => 0,
    'blogs'            => 0,
    'services_active'  => 0,
];
if ($pdo) {
    try {
        $stats['bookings_new']    = (int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE status='new'")->fetchColumn();
        $stats['bookings_total']  = (int)$pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
        $stats['bookings_today']  = (int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE DATE(created_at)=CURDATE()")->fetchColumn();
        $stats['bookings_week']   = (int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
        $stats['contacts_unread'] = (int)$pdo->query("SELECT COUNT(*) FROM contacts WHERE status='unread'")->fetchColumn();
        $stats['contacts_total']  = (int)$pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
        $stats['blogs']           = (int)$pdo->query("SELECT COUNT(*) FROM blog_posts WHERE is_published=1")->fetchColumn();
        $stats['services_active'] = (int)$pdo->query("SELECT COUNT(*) FROM services WHERE is_active=1")->fetchColumn();
    } catch (PDOException $e) { /* tables may not exist yet */ }
}

// ── Recent bookings ────────────────────────────────────────────────
$recent_bookings = [];
if ($pdo) {
    try {
        $recent_bookings = $pdo->query(
            "SELECT id, first_name, last_name, phone, email, city, pest_type, status, created_at
             FROM bookings ORDER BY created_at DESC LIMIT 10"
        )->fetchAll();
    } catch (PDOException $e) { /* ignore */ }
}

// ── Recent messages ────────────────────────────────────────────────
$recent_contacts = [];
if ($pdo) {
    try {
        $recent_contacts = $pdo->query(
            "SELECT id, name, email, status, created_at FROM contacts ORDER BY created_at DESC LIMIT 6"
        )->fetchAll();
    } catch (PDOException $e) { /* ignore */ }
}

// ── Booking breakdown by pest type ────────────────────────────────
$pest_breakdown = [];
if ($pdo) {
    try {
        $pest_breakdown = $pdo->query(
            "SELECT pest_type, COUNT(*) as cnt FROM bookings GROUP BY pest_type ORDER BY cnt DESC LIMIT 5"
        )->fetchAll();
    } catch (PDOException $e) { /* ignore */ }
}

// ── Status badge helper ───────────────────────────────────────────
function status_badge(string $status): string {
    return match($status) {
        'new'       => 'bg-accent/10 text-accent border border-accent/20',
        'contacted' => 'bg-blue-50 text-blue-700 border border-blue-200',
        'completed' => 'bg-yellow-50 text-green-700 border border-green-200',
        'cancelled' => 'bg-gray-100 text-gray-500 border border-gray-200',
        'unread'    => 'bg-accent/10 text-accent border border-accent/20',
        'read'      => 'bg-gray-100 text-gray-500 border border-gray-200',
        default     => 'bg-gray-100 text-gray-500 border border-gray-200',
    };
}

require_once BASE_DIR . '/admin/head.php';
require_once BASE_DIR . '/admin/sidebar.php';
?>

<main class="flex-grow overflow-auto bg-gray-50">

    <!-- ── Page Header ─────────────────────────────────────────── -->
    <div class="bg-white border-b border-gray-200 px-8 py-5 flex items-center justify-between sticky top-0 z-10">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-xs text-gray-400 mt-0.5">
                <?= date('l, F j, Y') ?>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <?php if ($stats['bookings_new'] > 0): ?>
            <a href="/general-pest-removal/admin/bookings?status=new"
               class="inline-flex items-center gap-2 bg-accent/10 hover:bg-accent/20 text-accent border border-accent/20 text-xs font-bold px-3.5 py-2 rounded-lg transition">
                <span class="w-2 h-2 rounded-full bg-accent inline-block"></span>
                <?= $stats['bookings_new'] ?> new <?= $stats['bookings_new'] === 1 ? 'booking' : 'bookings' ?>
            </a>
            <?php endif; ?>
            <a href="/general-pest-removal/admin/blogs/edit"
               class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:border-primary text-gray-700 hover:text-primary text-xs font-semibold px-3.5 py-2 rounded-lg transition">
                <i class="fa-solid fa-plus text-[10px]" aria-hidden="true"></i>
                New Post
            </a>
            <a href="/general-pest-removal/" target="_blank"
               class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:border-primary text-gray-500 hover:text-primary text-xs font-semibold px-3.5 py-2 rounded-lg transition">
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]" aria-hidden="true"></i>
                View Site
            </a>
        </div>
    </div>

    <div class="px-8 py-7 max-w-7xl mx-auto space-y-7">

        <?php if ($flash_success): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-2 text-sm">
            <i class="fa-solid fa-circle-check text-green-500" aria-hidden="true"></i>
            <?= htmlspecialchars($flash_success) ?>
        </div>
        <?php endif; ?>
        <?php if ($flash_error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2 text-sm">
            <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
            <?= htmlspecialchars($flash_error) ?>
        </div>
        <?php endif; ?>

        <!-- ── Stat Cards ─────────────────────────────────────── -->
        <div class="grid grid-cols-2 xl:grid-cols-5 gap-4">

            <!-- New Bookings — highlighted -->
            <a href="/general-pest-removal/admin/bookings?status=new"
               class="col-span-1 bg-white border border-gray-200 hover:border-accent/40 rounded-2xl p-5 flex items-start gap-4 transition hover:shadow-md group">
                <div class="w-11 h-11 rounded-xl bg-accent/10 flex items-center justify-center flex-shrink-0 group-hover:bg-accent/20 transition">
                    <i class="fa-solid fa-bell text-accent" aria-hidden="true"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">New Bookings</p>
                    <p class="text-3xl font-extrabold text-gray-900 leading-none"><?= $stats['bookings_new'] ?></p>
                    <p class="text-xs text-gray-400 mt-1"><?= $stats['bookings_today'] ?> today</p>
                </div>
            </a>

            <!-- Total Bookings -->
            <a href="/general-pest-removal/admin/bookings"
               class="bg-white border border-gray-200 hover:border-primary/40 rounded-2xl p-5 flex items-start gap-4 transition hover:shadow-md group">
                <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0 group-hover:bg-primary/20 transition">
                    <i class="fa-solid fa-calendar-check text-primary" aria-hidden="true"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Bookings</p>
                    <p class="text-3xl font-extrabold text-gray-900 leading-none"><?= $stats['bookings_total'] ?></p>
                    <p class="text-xs text-gray-400 mt-1"><?= $stats['bookings_week'] ?> this week</p>
                </div>
            </a>

            <!-- Unread Messages -->
            <a href="/general-pest-removal/admin/contacts"
               class="bg-white border border-gray-200 hover:border-secondary/40 rounded-2xl p-5 flex items-start gap-4 transition hover:shadow-md group">
                <div class="w-11 h-11 rounded-xl bg-secondary/10 flex items-center justify-center flex-shrink-0 group-hover:bg-secondary/20 transition">
                    <i class="fa-solid fa-envelope text-secondary" aria-hidden="true"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Unread Messages</p>
                    <p class="text-3xl font-extrabold text-gray-900 leading-none"><?= $stats['contacts_unread'] ?></p>
                    <p class="text-xs text-gray-400 mt-1"><?= $stats['contacts_total'] ?> total</p>
                </div>
            </a>

            <!-- Active Services -->
            <a href="/general-pest-removal/admin/services"
               class="bg-white border border-gray-200 hover:border-blue-400/40 rounded-2xl p-5 flex items-start gap-4 transition hover:shadow-md group">
                <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-100 transition">
                    <i class="fa-solid fa-shield-bug text-blue-600" aria-hidden="true"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Active Services</p>
                    <p class="text-3xl font-extrabold text-gray-900 leading-none"><?= $stats['services_active'] ?></p>
                    <p class="text-xs text-gray-400 mt-1">live on site</p>
                </div>
            </a>

            <!-- Published Posts -->
            <a href="/general-pest-removal/admin/blogs"
               class="bg-white border border-gray-200 hover:border-gray-400/40 rounded-2xl p-5 flex items-start gap-4 transition hover:shadow-md group">
                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0 group-hover:bg-gray-200 transition">
                    <i class="fa-solid fa-newspaper text-gray-600" aria-hidden="true"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Published Posts</p>
                    <p class="text-3xl font-extrabold text-gray-900 leading-none"><?= $stats['blogs'] ?></p>
                    <p class="text-xs text-gray-400 mt-1">blog articles</p>
                </div>
            </a>

        </div>

        <!-- ── Quick Actions ──────────────────────────────────── -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <?php
            $actions = [
                [
                    'href'    => '/general-pest-removal/admin/bookings',
                    'icon'    => 'fa-calendar-check',
                    'icon_bg' => 'bg-primary/10',
                    'icon_cl' => 'text-primary',
                    'label'   => 'Manage Bookings',
                    'sub'     => 'View &amp; update leads',
                ],
                [
                    'href'    => '/general-pest-removal/admin/services',
                    'icon'    => 'fa-shield-bug',
                    'icon_bg' => 'bg-blue-50',
                    'icon_cl' => 'text-blue-600',
                    'label'   => 'Manage Services',
                    'sub'     => 'Edit service offerings',
                ],
                [
                    'href'    => '/general-pest-removal/admin/seo',
                    'icon'    => 'fa-magnifying-glass',
                    'icon_bg' => 'bg-secondary/10',
                    'icon_cl' => 'text-secondary',
                    'label'   => 'SEO Settings',
                    'sub'     => 'Meta titles &amp; descriptions',
                ],
                [
                    'href'    => '/general-pest-removal/admin/blogs/edit',
                    'icon'    => 'fa-pen-to-square',
                    'icon_bg' => 'bg-accent/10',
                    'icon_cl' => 'text-accent',
                    'label'   => 'New Blog Post',
                    'sub'     => 'Create a new article',
                ],
            ];
            foreach ($actions as $a): ?>
            <a href="<?= $a['href'] ?>"
               class="bg-white border border-gray-200 hover:border-gray-300 rounded-2xl p-5 flex items-center gap-4 transition hover:shadow-md group">
                <div class="w-10 h-10 rounded-xl <?= $a['icon_bg'] ?> flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid <?= $a['icon'] ?> <?= $a['icon_cl'] ?> text-sm" aria-hidden="true"></i>
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-gray-900 text-sm leading-tight"><?= $a['label'] ?></p>
                    <p class="text-xs text-gray-400 mt-0.5"><?= $a['sub'] ?></p>
                </div>
                <i class="fa-solid fa-arrow-right text-gray-300 text-xs ml-auto group-hover:text-gray-500 transition" aria-hidden="true"></i>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- ── Main content grid ─────────────────────────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Recent Bookings (2/3 width) -->
            <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <h2 class="font-bold text-gray-900 text-sm">Recent Bookings</h2>
                        <?php if ($stats['bookings_new'] > 0): ?>
                        <span class="text-[11px] font-bold bg-accent text-white px-2 py-0.5 rounded-full">
                            <?= $stats['bookings_new'] ?> new
                        </span>
                        <?php endif; ?>
                    </div>
                    <a href="/general-pest-removal/admin/bookings"
                       class="text-xs text-primary hover:text-blue-800 font-semibold transition flex items-center gap-1">
                        View all
                        <i class="fa-solid fa-arrow-right text-[10px]" aria-hidden="true"></i>
                    </a>
                </div>

                <?php if (empty($recent_bookings)): ?>
                <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-calendar-xmark text-gray-400 text-xl" aria-hidden="true"></i>
                    </div>
                    <p class="text-sm font-semibold text-gray-700 mb-1">No bookings yet</p>
                    <p class="text-xs text-gray-400 mb-4">New leads will appear here when customers fill out the booking form.</p>
                    <a href="/general-pest-removal/" target="_blank"
                       class="text-xs font-semibold text-primary hover:text-blue-800 transition">
                        View booking form on site
                    </a>
                </div>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Customer</th>
                                <th class="text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider px-4 py-3 hidden sm:table-cell">Pest / Address</th>
                                <th class="text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider px-4 py-3">Status</th>
                                <th class="text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider px-4 py-3 hidden md:table-cell">Received</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php foreach ($recent_bookings as $b): ?>
                            <tr class="hover:bg-gray-50/70 transition <?= $b['status'] === 'new' ? 'bg-accent/[0.02]' : '' ?>">
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0 text-primary font-bold text-xs">
                                            <?= strtoupper(substr($b['first_name'], 0, 1)) ?>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 leading-tight truncate">
                                                <?= htmlspecialchars($b['first_name'] . ' ' . $b['last_name']) ?>
                                            </p>
                                            <p class="text-xs text-gray-400 truncate"><?= htmlspecialchars($b['phone']) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 hidden sm:table-cell">
                                    <p class="text-gray-700 capitalize font-medium text-xs"><?= htmlspecialchars($b['pest_type']) ?></p>
                                    <p class="text-gray-400 text-xs truncate max-w-[140px]"><?= htmlspecialchars($b['city']) ?></p>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center text-[11px] font-semibold px-2.5 py-1 rounded-full capitalize <?= status_badge($b['status']) ?>">
                                        <?= htmlspecialchars($b['status']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 hidden md:table-cell">
                                    <p class="text-xs text-gray-500"><?= date('M j', strtotime($b['created_at'])) ?></p>
                                    <p class="text-xs text-gray-400"><?= date('g:i a', strtotime($b['created_at'])) ?></p>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="/general-pest-removal/admin/bookings?view=<?= $b['id'] ?>"
                                       class="text-xs font-semibold text-primary hover:text-blue-800 transition whitespace-nowrap">
                                        Open
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right column: Messages + Pest breakdown -->
            <div class="space-y-5">

                <!-- Recent Messages -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <div class="flex items-center gap-2.5">
                            <h2 class="font-bold text-gray-900 text-sm">Messages</h2>
                            <?php if ($stats['contacts_unread'] > 0): ?>
                            <span class="text-[11px] font-bold bg-secondary text-white px-2 py-0.5 rounded-full">
                                <?= $stats['contacts_unread'] ?> unread
                            </span>
                            <?php endif; ?>
                        </div>
                        <a href="/general-pest-removal/admin/contacts"
                           class="text-xs text-primary hover:text-blue-800 font-semibold transition flex items-center gap-1">
                            View all
                            <i class="fa-solid fa-arrow-right text-[10px]" aria-hidden="true"></i>
                        </a>
                    </div>

                    <?php if (empty($recent_contacts)): ?>
                    <div class="flex flex-col items-center justify-center py-10 px-5 text-center">
                        <i class="fa-solid fa-inbox text-gray-300 text-2xl mb-2" aria-hidden="true"></i>
                        <p class="text-xs text-gray-400">No messages yet.</p>
                    </div>
                    <?php else: ?>
                    <ul class="divide-y divide-gray-50">
                        <?php foreach ($recent_contacts as $c): ?>
                        <li>
                            <a href="/general-pest-removal/admin/contacts?view=<?= $c['id'] ?>"
                               class="flex items-start gap-3 px-5 py-3.5 hover:bg-gray-50 transition">
                                <div class="w-7 h-7 rounded-full bg-secondary/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <span class="text-secondary font-bold text-[10px]">
                                        <?= strtoupper(substr($c['name'], 0, 1)) ?>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-grow">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-xs font-semibold text-gray-900 truncate <?= $c['status'] === 'unread' ? 'text-gray-900' : 'text-gray-600' ?>">
                                            <?= htmlspecialchars($c['name']) ?>
                                        </p>
                                        <span class="text-[10px] text-gray-400 flex-shrink-0">
                                            <?= date('M j', strtotime($c['created_at'])) ?>
                                        </span>
                                    </div>
                                    <?php if (!empty($c['subject'])): ?>
                                    <p class="text-[11px] text-gray-400 truncate mt-0.5"><?= htmlspecialchars($c['subject']) ?></p>
                                    <?php endif; ?>
                                    <?php if ($c['status'] === 'unread'): ?>
                                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-accent mt-1"></span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>

                <!-- Top Pest Types -->
                <?php if (!empty($pest_breakdown)): ?>
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="font-bold text-gray-900 text-sm">Top Pest Requests</h2>
                        <p class="text-xs text-gray-400 mt-0.5">All-time booking breakdown</p>
                    </div>
                    <div class="px-5 py-4 space-y-3">
                        <?php
                        $max = max(array_column($pest_breakdown, 'cnt')) ?: 1;
                        $pest_colors = [
                            'bedbugs'     => ['bg-red-500',    'bg-red-50',    'text-red-600'],
                            'rodents'     => ['bg-orange-500', 'bg-orange-50', 'text-orange-600'],
                            'cockroaches' => ['bg-amber-500',  'bg-amber-50',  'text-amber-600'],
                            'ants'        => ['bg-yellow-500', 'bg-yellow-50', 'text-yellow-600'],
                            'wasps'       => ['bg-primary',    'bg-blue-50',   'text-primary'],
                            'wildlife'    => ['bg-secondary',  'bg-yellow-50',  'text-secondary'],
                            'other'       => ['bg-gray-400',   'bg-gray-50',   'text-gray-600'],
                        ];
                        foreach ($pest_breakdown as $p):
                            $pct    = round(($p['cnt'] / $max) * 100);
                            $colors = $pest_colors[$p['pest_type']] ?? $pest_colors['other'];
                        ?>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-semibold text-gray-700 capitalize"><?= htmlspecialchars($p['pest_type']) ?></span>
                                <span class="text-xs font-bold <?= $colors[2] ?>"><?= $p['cnt'] ?></span>
                            </div>
                            <div class="h-1.5 w-full <?= $colors[1] ?> rounded-full overflow-hidden">
                                <div class="h-full <?= $colors[0] ?> rounded-full" style="width:<?= $pct ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Site Config shortcut -->
                <div class="bg-dark rounded-2xl p-5">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-gear text-white text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="font-bold text-white text-sm leading-tight">Site Settings</p>
                            <p class="text-xs text-gray-400 mt-0.5">Logo, phone, hours, socials</p>
                        </div>
                    </div>
                    <a href="/general-pest-removal/admin/config"
                       class="w-full flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 border border-white/10 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition">
                        <i class="fa-solid fa-sliders text-[11px]" aria-hidden="true"></i>
                        Configure Site
                    </a>
                </div>

            </div>
        </div>

    </div>
</main>

</body>
</html>
