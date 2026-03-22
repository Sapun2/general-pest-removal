<?php
/**
 * Dynamic XML Sitemap
 * Route: /sitemap.xml → index.php → this file
 * Change $site_url when going live
 */
header('Content-Type: application/xml; charset=utf-8');

if (!defined('BASE_DIR')) define('BASE_DIR', __DIR__);
require_once __DIR__ . '/includes/db.php';

$site_url = defined('SITE_BASE_URL') ? SITE_BASE_URL : 'http://localhost:8888/general-pest-removal';

// Static pages
$static = [
    ['loc' => '/',         'priority' => '1.0',  'changefreq' => 'weekly'],
    ['loc' => '/services', 'priority' => '0.9',  'changefreq' => 'monthly'],
    ['loc' => '/booking',  'priority' => '0.9',  'changefreq' => 'monthly'],
    ['loc' => '/faq',      'priority' => '0.8',  'changefreq' => 'monthly'],
    ['loc' => '/blogs',    'priority' => '0.8',  'changefreq' => 'weekly'],
    ['loc' => '/about',    'priority' => '0.7',  'changefreq' => 'monthly'],
    ['loc' => '/contact',  'priority' => '0.7',  'changefreq' => 'monthly'],
];

// Dynamic blog posts
$blog_posts = [];
if (isset($pdo) && $pdo) {
    try {
        $blog_posts = $pdo->query(
            "SELECT slug, updated_at, published_at FROM blog_posts WHERE is_published = 1 ORDER BY published_at DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Fail silently — sitemap still works for static pages
    }
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

<?php foreach ($static as $page): ?>
    <url>
        <loc><?= htmlspecialchars($site_url . $page['loc']) ?></loc>
        <changefreq><?= $page['changefreq'] ?></changefreq>
        <priority><?= $page['priority'] ?></priority>
        <lastmod><?= date('Y-m-d') ?></lastmod>
    </url>
<?php endforeach; ?>

<?php foreach ($blog_posts as $post): ?>
    <url>
        <loc><?= htmlspecialchars($site_url . '/blog/' . $post['slug']) ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
        <lastmod><?= date('Y-m-d', strtotime($post['updated_at'] ?? $post['published_at'])) ?></lastmod>
    </url>
<?php endforeach; ?>

</urlset>
