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

function isActive($path, $uri) {
    if ($path === '/' && ($uri === '/sydney-pest-removal/' || $uri === '/sydney-pest-removal' || $uri === '/sydney-pest-removal/index.php' || $uri === '/generalpestremoval/' || $uri === '/generalpestremoval')) {
        return true;
    }
    if ($path !== '/' && strpos($uri, $path) !== false) {
        return true;
    }
    return false;
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
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?= htmlspecialchars($_sc['gtm_id']) ?>');</script>
    <?php endif; ?>

    <?php if (!empty($_sc['ga4_id'])): ?>
    <!-- Google Analytics GA4 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($_sc['ga4_id']) ?>"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= htmlspecialchars($_sc['ga4_id']) ?>');</script>
    <?php endif; ?>

    <?php if (!empty($_sc['gads_id'])): ?>
    <!-- Google Ads -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($_sc['gads_id']) ?>"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= htmlspecialchars($_sc['gads_id']) ?>');</script>
    <?php endif; ?>

    <?php if (!empty($_sc['meta_pixel_id'])): ?>
    <!-- Meta Pixel -->
    <script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','<?= htmlspecialchars($_sc['meta_pixel_id']) ?>');fbq('track','PageView');</script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?= htmlspecialchars($_sc['meta_pixel_id']) ?>&ev=PageView&noscript=1" alt=""></noscript>
    <?php endif; ?>

    <!-- Google Fonts: Nunito -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary:   '#16a34a',
                        secondary: '#16a34a',
                        accent:    '#b45309',
                        dark:      '#1f2937'
                    },
                    fontFamily: {
                        sans: ['Nunito', 'system-ui', '-apple-system', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Smooth accordion transitions */
        details > div { animation: fadeIn .15s ease; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }
    </style>
</head>
<body class="font-sans antialiased text-gray-800 bg-white flex flex-col min-h-screen">
    <?php if (!empty($_sc['gtm_id'])): ?>
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= htmlspecialchars($_sc['gtm_id']) ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <?php endif; ?>

    <!-- Top Bar -->
    <div class="bg-dark text-gray-400 text-xs py-2 px-4 hidden md:block border-b border-white/5">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-5">
                <span class="flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check text-secondary text-xs" aria-hidden="true"></i>
                    <?= htmlspecialchars($_sc['hours_emergency'] ?? '24/7 Emergency Response') ?> Across Sydney &amp; Brisbane
                </span>
                <span class="text-white/20">|</span>
                <span class="flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-halved text-secondary text-xs" aria-hidden="true"></i>
                    Licensed &amp; Fully Insured
                </span>
            </div>
            <div class="flex items-center gap-5">
                <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>" class="hover:text-white transition font-semibold text-gray-300">
                    <i class="fa-solid fa-phone mr-1.5 text-secondary" aria-hidden="true"></i><?= htmlspecialchars($site_phone) ?>
                </a>
                <?php if (!empty($_sc['phone_secondary']) && !empty($_sc['phone_secondary_raw'])): ?>
                <a href="tel:<?= htmlspecialchars($_sc['phone_secondary_raw']) ?>" class="hover:text-white transition text-gray-400">
                    <i class="fa-solid fa-phone mr-1.5 text-secondary" aria-hidden="true"></i><?= htmlspecialchars($_sc['phone_secondary']) ?>
                </a>
                <?php endif; ?>
                <a href="<?= $base_url ?>/contact" class="hover:text-white transition">
                    <?= htmlspecialchars($site_email) ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-[70px]">

                <!-- Logo -->
                <a href="<?= $base_url ?>/" class="flex items-center gap-2.5 group" aria-label="<?= htmlspecialchars($site_name) ?> — Home">
                    <?php if (($_sc['logo_type'] ?? 'text') === 'image' && !empty($_sc['logo_image_url'])):
                    // Prefix $base_url for relative paths; leave absolute URLs (https://...) untouched
                    $logo_img_src = preg_match('#^https?://#', $_sc['logo_image_url'])
                        ? $_sc['logo_image_url']
                        : $base_url . $_sc['logo_image_url'];
                ?>
                    <img src="<?= htmlspecialchars($logo_img_src) ?>"
                         alt="<?= htmlspecialchars($site_name) ?>"
                         class="h-9 w-auto object-contain">
                    <?php else: ?>
                    <div class="w-9 h-9 bg-primary rounded-lg flex items-center justify-center group-hover:bg-yellow-900 transition">
                        <i class="fa-solid <?= htmlspecialchars($_sc['logo_icon'] ?? 'fa-bug') ?> text-white text-sm" aria-hidden="true"></i>
                    </div>
                    <span class="text-xl font-bold text-dark tracking-tight">
                        <?= htmlspecialchars($_sc['logo_text_primary'] ?? 'General') ?><span class="text-primary"><?= htmlspecialchars($_sc['logo_text_secondary'] ?? 'Pest') ?></span>
                    </span>
                    <?php endif; ?>
                </a>

                <!-- Desktop Menu -->
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
                       class="px-4 py-2 rounded-lg text-sm font-medium transition
                              <?= $active
                                  ? 'text-primary bg-green-50 font-semibold'
                                  : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' ?>">
                        <?= $link['label'] ?>
                    </a>
                    <?php endforeach; ?>
                    <a href="<?= $base_url ?>/booking"
                       class="ml-3 bg-accent hover:bg-yellow-800 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition shadow-sm flex items-center gap-2">
                        <i class="fa-regular fa-calendar-check text-xs" aria-hidden="true"></i>
                        Book Online
                    </a>
                </nav>

                <!-- Mobile menu button -->
                <button id="mobile-menu-btn"
                        class="md:hidden p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition"
                        aria-label="Open menu" aria-expanded="false" aria-controls="mobile-menu">
                    <i class="fa-solid fa-bars text-lg" aria-hidden="true" id="mobile-menu-icon"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white" role="navigation" aria-label="Mobile navigation">
            <div class="px-4 py-4 space-y-1">
                <?php foreach ($nav_links as $link):
                    $active = isActive($link['href'], $request_uri);
                ?>
                <a href="<?= $base_url . $link['href'] ?>"
                   class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition
                          <?= $active ? 'text-primary bg-green-50' : 'text-gray-700 hover:bg-gray-50' ?>">
                    <?= $link['label'] ?>
                </a>
                <?php endforeach; ?>
                <div class="pt-3 border-t border-gray-100 mt-3">
                    <a href="<?= $base_url ?>/booking"
                       class="flex items-center justify-center gap-2 bg-accent text-white font-semibold px-4 py-3 rounded-lg transition hover:bg-yellow-800">
                        <i class="fa-regular fa-calendar-check" aria-hidden="true"></i>
                        Book a Free Inspection
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
                icon.className = open ? 'fa-solid fa-bars text-lg' : 'fa-solid fa-xmark text-lg';
                btn.setAttribute('aria-expanded', String(!open));
            });
        })();
    </script>
