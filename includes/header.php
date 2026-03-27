<?php
/**
 * Global Header
 * Pages set $page_seo array BEFORE including this file.
 */

if (!defined('SITE_BASE_URL')) {
    require_once BASE_DIR . '/config.php';
}
if (!function_exists('get_page_seo')) {
    require_once BASE_DIR . '/includes/db.php';
    require_once BASE_DIR . '/includes/seo-meta.php';
}

$_sc            = $GLOBALS['site_config'] ?? [];
$base_url       = SITE_BASE_URL;
$site_name      = $_sc['business_name']    ?? SITE_NAME;
$site_phone     = $_sc['phone_primary']    ?? SITE_PHONE;
$site_phone_raw = $_sc['phone_primary_raw'] ?? SITE_PHONE_RAW;
$site_email     = $_sc['email_primary']    ?? SITE_EMAIL;

$seo             = $page_seo ?? [];
$seo_title       = !empty($seo['title'])          ? $seo['title']          : "$site_name | Sydney & Brisbane's Trusted Pest Removal Specialists";
$seo_description = !empty($seo['description'])    ? $seo['description']    : "Expert pest removal across Sydney & Brisbane. Cockroaches, termites, spiders, rodents & bed bugs. Licensed & insured. Book online.";
$seo_canonical   = !empty($seo['canonical'])      ? $seo['canonical']      : $base_url . strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
$seo_og_title    = !empty($seo['og_title'])       ? $seo['og_title']       : $seo_title;
$seo_og_desc     = !empty($seo['og_description']) ? $seo['og_description'] : $seo_description;
$seo_og_image    = !empty($seo['og_image'])       ? $seo['og_image']       : $base_url . '/assets/images/og-default.jpg';
$seo_noindex     = !empty($seo['noindex']);
$seo_schema      = $seo['schema']      ?? null;
$seo_breadcrumbs = $seo['breadcrumbs'] ?? null;

$breadcrumb_schema = null;
if ($seo_breadcrumbs) {
    $items = [];
    foreach ($seo_breadcrumbs as $pos => $crumb) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $pos + 1,
            'name'     => $crumb['name'],
            'item'     => $base_url . $crumb['url'],
        ];
    }
    $breadcrumb_schema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    ];
}

$request_uri = $_SERVER['REQUEST_URI'];

function isActive(string $path, string $uri): bool {
    $base  = rtrim(defined('SITE_BASE_PATH') ? SITE_BASE_PATH : '', '/');
    $clean = rtrim(strtok($uri, '?') ?: '/', '/');
    if ($path === '/') {
        // Match domain root (production: '') or MAMP subdir (e.g. '/general-pest-removal')
        return $clean === $base || $clean === '';
    }
    $target = $base . $path;
    // Exact match or matches as a path prefix (e.g. /services/termites → /services active)
    return $clean === $target || strpos($clean, $target . '/') === 0;
}
?>
<!DOCTYPE html>
<html lang="en-AU" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($seo_title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($seo_description) ?>">
    <link rel="canonical" href="<?= htmlspecialchars($seo_canonical) ?>">
    <?php if ($seo_noindex): ?>
    <meta name="robots" content="noindex, nofollow">
    <?php else: ?>
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <?php endif; ?>

    <meta property="og:type"        content="website">
    <meta property="og:site_name"   content="<?= htmlspecialchars($site_name) ?>">
    <meta property="og:title"       content="<?= htmlspecialchars($seo_og_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($seo_og_desc) ?>">
    <meta property="og:url"         content="<?= htmlspecialchars($seo_canonical) ?>">
    <meta property="og:image"       content="<?= htmlspecialchars($seo_og_image) ?>">
    <meta property="og:locale"      content="en_AU">

    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?= htmlspecialchars($seo_og_title) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($seo_og_desc) ?>">
    <meta name="twitter:image"       content="<?= htmlspecialchars($seo_og_image) ?>">

    <?php if ($seo_schema): ?>
    <script type="application/ld+json">
    <?= json_encode($seo_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?>
    </script>
    <?php endif; ?>

    <?php if ($breadcrumb_schema): ?>
    <script type="application/ld+json">
    <?= json_encode($breadcrumb_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?>
    </script>
    <?php endif; ?>

    <?php if (!empty($_sc['gsc_verification'])): ?>
    <meta name="google-site-verification" content="<?= htmlspecialchars($_sc['gsc_verification']) ?>">
    <?php endif; ?>

    <?php if (!empty($_sc['gtm_id'])): ?>
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?= htmlspecialchars($_sc['gtm_id']) ?>');</script>
    <?php endif; ?>

    <?php if (!empty($_sc['ga4_id'])): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($_sc['ga4_id']) ?>"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= htmlspecialchars($_sc['ga4_id']) ?>');</script>
    <?php endif; ?>

    <?php if (!empty($_sc['meta_pixel_id'])): ?>
    <script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','<?= htmlspecialchars($_sc['meta_pixel_id']) ?>');fbq('track','PageView');</script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?= htmlspecialchars($_sc['meta_pixel_id']) ?>&ev=PageView&noscript=1" alt=""></noscript>
    <?php endif; ?>

    <?php if (!empty($_sc['favicon_url'])): ?>
    <?php $fav_url = preg_match('#^https?://#', $_sc['favicon_url']) ? $_sc['favicon_url'] : $base_url . $_sc['favicon_url']; ?>
    <link rel="icon" href="<?= htmlspecialchars($fav_url) ?>">
    <link rel="shortcut icon" href="<?= htmlspecialchars($fav_url) ?>">
    <link rel="apple-touch-icon" href="<?= htmlspecialchars($fav_url) ?>">
    <?php endif; ?>

    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary:   '#16a34a',
                        secondary: '#16a34a',
                        dark:      '#0f172a',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Accordion animation */
        details > div { animation: fadeIn .15s ease; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }
        /* Smooth mobile menu */
        #mobile-menu { transition: none; }
    </style>
</head>
<body class="font-sans antialiased text-slate-800 bg-white flex flex-col min-h-screen">
    <?php if (!empty($_sc['gtm_id'])): ?>
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= htmlspecialchars($_sc['gtm_id']) ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <?php endif; ?>

    <!-- Top Bar — desktop only -->
    <div class="hidden md:block bg-dark border-b border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-10">
            <div class="flex items-center gap-5 text-xs text-slate-400">
                <span class="flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check text-green-500 text-[10px]" aria-hidden="true"></i>
                    24/7 Emergency Response · Sydney &amp; Brisbane
                </span>
                <span class="text-slate-700">|</span>
                <span class="flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-halved text-green-500 text-[10px]" aria-hidden="true"></i>
                    Licensed &amp; Fully Insured
                </span>
            </div>
            <div class="flex items-center gap-6 text-xs text-slate-400">
                <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                   class="flex items-center gap-1.5 hover:text-white transition font-medium">
                    <i class="fa-solid fa-phone text-green-500 text-[10px]" aria-hidden="true"></i>
                    <?= htmlspecialchars($site_phone) ?>
                </a>
                <a href="mailto:<?= htmlspecialchars($site_email) ?>"
                   class="hover:text-white transition">
                    <?= htmlspecialchars($site_email) ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <header class="bg-white border-b border-slate-100 sticky top-0 z-50 shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <!-- Logo -->
                <a href="<?= $base_url ?>/" class="flex items-center gap-2.5 flex-shrink-0" aria-label="<?= htmlspecialchars($site_name) ?> Home">
                    <?php
                    if (($_sc['logo_type'] ?? 'text') === 'image' && !empty($_sc['logo_image_url'])):
                        $logo_img_src = preg_match('#^https?://#', $_sc['logo_image_url'])
                            ? $_sc['logo_image_url']
                            : $base_url . $_sc['logo_image_url'];
                    ?>
                    <img src="<?= htmlspecialchars($logo_img_src) ?>"
                         alt="<?= htmlspecialchars($site_name) ?>"
                         class="h-8 w-auto object-contain">
                    <?php else: ?>
                    <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid <?= htmlspecialchars($_sc['logo_icon'] ?? 'fa-bug') ?> text-white text-xs" aria-hidden="true"></i>
                    </div>
                    <span class="text-lg font-bold text-slate-900 tracking-tight leading-none">
                        <?= htmlspecialchars($_sc['logo_text_primary'] ?? 'General') ?><span class="text-green-600"><?= htmlspecialchars($_sc['logo_text_secondary'] ?? 'Pest') ?></span>
                    </span>
                    <?php endif; ?>
                </a>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center gap-1" aria-label="Main navigation">
                    <?php
                    $nav_links = [
                        ['href' => '/',        'label' => 'Home'],
                        ['href' => '/services','label' => 'Services'],
                        ['href' => '/about',   'label' => 'About'],
                        ['href' => '/blogs',   'label' => 'Blog'],
                        ['href' => '/faq',     'label' => 'FAQ'],
                        ['href' => '/contact', 'label' => 'Contact'],
                    ];
                    foreach ($nav_links as $link):
                        $active = isActive($link['href'], $request_uri);
                    ?>
                    <a href="<?= $base_url . $link['href'] ?>"
                       class="px-3 py-2 text-sm font-medium transition rounded-md <?= $active ? 'text-green-600 bg-green-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' ?>">
                        <?= $link['label'] ?>
                    </a>
                    <?php endforeach; ?>
                    <a href="<?= $base_url ?>/booking"
                       class="ml-4 inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
                        <i class="fa-regular fa-calendar-check text-xs" aria-hidden="true"></i>
                        Book Free Inspection
                    </a>
                </nav>

                <!-- Mobile burger -->
                <button id="mobile-menu-btn"
                        class="md:hidden w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition"
                        aria-label="Open menu" aria-expanded="false" aria-controls="mobile-menu">
                    <i class="fa-solid fa-bars text-base" aria-hidden="true" id="mobile-menu-icon"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu"
             class="hidden md:hidden border-t border-slate-100 bg-white"
             role="navigation" aria-label="Mobile navigation">
            <div class="px-4 py-4 space-y-1">
                <?php foreach ($nav_links as $link):
                    $active = isActive($link['href'], $request_uri);
                ?>
                <a href="<?= $base_url . $link['href'] ?>"
                   class="flex items-center px-3 py-3 rounded-lg text-sm font-medium transition <?= $active ? 'text-green-600 bg-green-50' : 'text-slate-700 hover:bg-slate-50' ?>">
                    <?= $link['label'] ?>
                </a>
                <?php endforeach; ?>
                <div class="pt-3 mt-2 border-t border-slate-100">
                    <a href="<?= $base_url ?>/booking"
                       class="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-3.5 rounded-lg transition text-sm">
                        <i class="fa-regular fa-calendar-check" aria-hidden="true"></i>
                        Book a Free Inspection
                    </a>
                    <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                       class="flex items-center justify-center gap-2 mt-2 border border-slate-200 text-slate-700 font-semibold px-4 py-3 rounded-lg transition text-sm hover:bg-slate-50">
                        <i class="fa-solid fa-phone text-green-600 text-xs" aria-hidden="true"></i>
                        <?= htmlspecialchars($site_phone) ?>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <script>
        (function () {
            var btn  = document.getElementById('mobile-menu-btn');
            var menu = document.getElementById('mobile-menu');
            var icon = document.getElementById('mobile-menu-icon');
            btn.addEventListener('click', function () {
                var open = !menu.classList.contains('hidden');
                menu.classList.toggle('hidden', open);
                icon.className = open ? 'fa-solid fa-bars text-base' : 'fa-solid fa-xmark text-base';
                btn.setAttribute('aria-expanded', String(!open));
            });
        })();
    </script>
