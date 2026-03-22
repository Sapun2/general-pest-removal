<?php
/**
 * Shared Admin Sidebar
 * Include AFTER session_start() in every admin page.
 * Set $active_page to one of: dashboard, seo, blogs, bookings, contacts, services, config
 */
$active_page = $active_page ?? 'dashboard';

// Resolve logo from site_config (same logic as public header/footer)
$_asc         = $GLOBALS['site_config'] ?? [];
$_a_logo_type = $_asc['logo_type'] ?? 'text';
$_a_logo_url  = '';
if ($_a_logo_type === 'image' && !empty($_asc['logo_image_url'])) {
    $base = defined('SITE_BASE_URL') ? SITE_BASE_URL : '';
    $_a_logo_url = preg_match('#^https?://#', $_asc['logo_image_url'])
        ? $_asc['logo_image_url']
        : $base . $_asc['logo_image_url'];
}
$_a_logo_icon       = $_asc['logo_icon']           ?? 'fa-bug';
$_a_logo_primary    = $_asc['logo_text_primary']   ?? 'General';
$_a_logo_secondary  = $_asc['logo_text_secondary'] ?? 'Pest';

$nav_items = [
    'dashboard' => ['href' => '/admin',                  'icon' => 'fa-gauge-high',         'label' => 'Dashboard'],
    'bookings'  => ['href' => '/admin/bookings',         'icon' => 'fa-calendar-check',     'label' => 'Bookings'],
    'contacts'  => ['href' => '/admin/contacts',         'icon' => 'fa-envelope-open-text', 'label' => 'Messages'],
    'services'  => ['href' => '/admin/services',         'icon' => 'fa-shield-bug',         'label' => 'Services'],
    'blogs'     => ['href' => '/admin/blogs',            'icon' => 'fa-newspaper',          'label' => 'Blog Posts'],
    'seo'       => ['href' => '/admin/seo',              'icon' => 'fa-magnifying-glass',   'label' => 'SEO Settings'],
    'config'    => ['href' => '/admin/config',           'icon' => 'fa-gear',               'label' => 'Site Config'],
];
?>
<aside class="w-64 bg-dark text-gray-300 flex flex-col min-h-screen sticky top-0 flex-shrink-0">
    <!-- Logo -->
    <div class="p-5 border-b border-gray-800">
        <a href="/" class="flex items-center gap-2.5 mb-1" title="View Website">
            <?php if ($_a_logo_url): ?>
            <img src="<?= htmlspecialchars($_a_logo_url) ?>"
                 alt="<?= htmlspecialchars($_a_logo_primary . $_a_logo_secondary) ?>"
                 class="h-8 w-auto object-contain brightness-0 invert">
            <?php else: ?>
            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fa-solid <?= htmlspecialchars($_a_logo_icon) ?> text-white text-xs" aria-hidden="true"></i>
            </div>
            <span class="text-lg font-extrabold text-white leading-none">
                <?= htmlspecialchars($_a_logo_primary) ?><span class="text-secondary"><?= htmlspecialchars($_a_logo_secondary) ?></span>
            </span>
            <?php endif; ?>
        </a>
        <p class="text-xs text-gray-600">Admin Panel</p>
    </div>

    <!-- Navigation -->
    <nav class="flex-grow p-4 space-y-1" aria-label="Admin navigation">
        <?php foreach ($nav_items as $key => $item): ?>
        <a href="<?= $item['href'] ?>"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition
                  <?= $key === $active_page
                      ? 'bg-primary text-white'
                      : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?>">
            <i class="fa-solid <?= $item['icon'] ?> w-4 text-center" aria-hidden="true"></i>
            <?= $item['label'] ?>
        </a>
        <?php endforeach; ?>

        <hr class="border-gray-800 my-3">

        <a href="/"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
            <i class="fa-solid fa-globe w-4 text-center" aria-hidden="true"></i> View Website
        </a>
        <a href="/admin/logout"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-red-400 hover:bg-red-900 hover:text-white transition">
            <i class="fa-solid fa-right-from-bracket w-4 text-center" aria-hidden="true"></i> Logout
        </a>
    </nav>

    <!-- Footer -->
    <div class="p-4 border-t border-gray-800 text-xs text-gray-600">
        Logged in as <span class="text-gray-400 font-medium"><?= htmlspecialchars($_SESSION['admin_username'] ?? 'admin') ?></span>
    </div>
</aside>
