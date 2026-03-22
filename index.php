<?php
// Base folder for local dev. Change to: $base_path = ''; when going live on a domain.
define('BASE_DIR', __DIR__);
// Auto-detect base path: /general-pest-removal locally under MAMP, empty on production
$base_path = (strpos($_SERVER['REQUEST_URI'] ?? '/', '/general-pest-removal') === 0) ? '/general-pest-removal' : '';

// Strip base path from URI
$request_uri = $_SERVER['REQUEST_URI'];
if (strpos($request_uri, $base_path) === 0) {
    $request_uri = substr($request_uri, strlen($base_path));
}

// Ignore query strings
$parsed_url = parse_url($request_uri);
$path = $parsed_url['path'] ?? '/';

// Remove trailing slashes
if ($path !== '/' && substr($path, -1) === '/') {
    $path = rtrim($path, '/');
}

// ─────────────────────────────────────────────
// 1. DYNAMIC ROUTES (pattern matching)
// ─────────────────────────────────────────────

// /booking/{pest-type} → auto-fills booking form
if (preg_match('#^/booking/([a-zA-Z0-9_-]+)$#', $path, $matches)) {
    $_GET['service'] = $matches[1];
    require 'pages/booking.php';
    exit;
}

// /blog/{slug} → single blog article
if (preg_match('#^/blog/([a-zA-Z0-9_-]+)$#', $path, $matches)) {
    $_GET['slug'] = $matches[1];
    require 'pages/single-blog.php';
    exit;
}

// /admin/* → admin panel (must come before static routes)
if (preg_match('#^/admin(/.*)?$#', $path, $matches)) {
    $admin_sub = $matches[1] ?? '';
    switch (rtrim($admin_sub, '/')) {
        case '':
        case '/index':
            require 'admin/index.php';
            break;
        case '/login':
            require 'admin/login.php';
            break;
        case '/logout':
            require 'admin/logout.php';
            break;
        case '/seo':
            require 'admin/seo/index.php';
            break;
        case '/blogs':
            require 'admin/blogs/index.php';
            break;
        case '/blogs/edit':
            require 'admin/blogs/edit.php';
            break;
        case '/blog-edit':
            // Legacy redirect for backwards compatibility
            header('Location: /admin/blogs/edit' . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
            exit;
        case '/bookings':
            require 'admin/bookings/index.php';
            break;
        case '/contacts':
            require 'admin/contacts/index.php';
            break;
        case '/services':
            require 'admin/services/index.php';
            break;
        case '/services/edit':
            require 'admin/services/edit.php';
            break;
        case '/config':
            require 'admin/config/index.php';
            break;
        case '/setup':
            require 'admin/setup.php';
            break;
        case '/upload-image':
            require 'admin/upload-image.php';
            break;
        default:
            require 'admin/index.php';
    }
    exit;
}

// ─────────────────────────────────────────────
// 2. STATIC ROUTES
// ─────────────────────────────────────────────

switch ($path) {
    case '':
    case '/':
        require 'pages/home.php';
        break;

    case '/about':
        require 'pages/about.php';
        break;

    case '/services':
        require 'pages/services.php';
        break;

    case '/booking':
        require 'pages/booking.php';
        break;

    case '/blogs':
    case '/blog':
        require 'pages/blogs.php';
        break;

    case '/faq':
        require 'pages/faq.php';
        break;

    case '/contact':
        require 'pages/contact.php';
        break;

    case '/thank-you':
        require 'pages/thank-you.php';
        break;

    case '/sitemap.xml':
        require 'sitemap.php';
        break;

    // Form handlers
    case '/process_booking':
    case '/process_booking.php':
        require 'process_booking.php';
        break;

    case '/process_contact':
    case '/process_contact.php':
        require 'process_contact.php';
        break;

    // ── 404 ──
    default:
        http_response_code(404);
        $page_seo = ['title' => 'Page Not Found | General Pest Removal', 'noindex' => true];
        require 'includes/header.php';
        echo '<main class="flex-grow flex items-center justify-center py-20 text-center">
                <div>
                    <i class="fa-solid fa-bug text-8xl text-gray-200 mb-6 block" aria-hidden="true"></i>
                    <h1 class="text-6xl font-black text-primary mb-4">404</h1>
                    <p class="text-xl text-gray-600 mb-8">Oops! The page you are looking for does not exist.</p>
                    <div class="flex gap-4 justify-center">
                        <a href="' . $base_path . '/" class="bg-primary text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-900 transition">Go Home</a>
                        <a href="' . $base_path . '/booking" class="bg-accent text-white px-6 py-3 rounded-lg font-bold hover:bg-orange-700 transition">Book Online</a>
                    </div>
                </div>
              </main>';
        require 'includes/footer.php';
        break;
}
