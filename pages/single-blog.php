<?php
require_once BASE_DIR . '/includes/db.php';
require_once BASE_DIR . '/includes/seo-meta.php';

$slug = isset($_GET['slug']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['slug']) : '';

$post = null;
if ($slug && isset($pdo) && $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE slug = ? AND is_published = 1 LIMIT 1");
        $stmt->execute([$slug]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
}

if (!$post) {
    http_response_code(404);
    $page_seo = ['title' => 'Article Not Found | General Pest Removal Blog', 'noindex' => true];
    require_once BASE_DIR . '/includes/header.php';
    echo '<main class="flex-grow flex items-center justify-center py-28 text-center">
            <div class="max-w-lg mx-auto px-4">
                <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-newspaper text-3xl text-gray-400" aria-hidden="true"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight mb-4">Article Not Found</h1>
                <p class="text-gray-500 mb-8">This post may have been moved or does not exist yet.</p>
                <div class="flex flex-wrap justify-center gap-3">
                    <a href="' . $base_url . '/blogs" class="bg-primary text-white text-sm font-semibold px-6 py-3 rounded-lg hover:bg-yellow-900 transition">View All Articles</a>
                    <a href="' . $base_url . '/booking" class="bg-accent text-white text-sm font-semibold px-6 py-3 rounded-lg hover:bg-yellow-800 transition">Book Inspection</a>
                </div>
            </div>
          </main>';
    require_once BASE_DIR . '/includes/footer.php';
    exit;
}

$meta_title = !empty($post['meta_title'])       ? $post['meta_title']       : $post['title'] . ' | General Pest Removal Blog';
$meta_desc  = !empty($post['meta_description']) ? $post['meta_description'] : substr(strip_tags($post['excerpt']), 0, 155);
$og_image   = !empty($post['og_image'])         ? $post['og_image']         : (!empty($post['featured_image']) ? SITE_BASE_URL . $post['featured_image'] : '');

$pub_date = date('Y-m-d', strtotime($post['published_at']));
$mod_date = date('Y-m-d', strtotime($post['updated_at'] ?? $post['published_at']));

$page_seo = get_page_seo('blog_' . $slug, [
    'title'       => $meta_title,
    'description' => $meta_desc,
    'canonical'   => SITE_BASE_URL . '/blog/' . $slug,
    'og_title'    => $post['title'],
    'og_image'    => $og_image,
    'breadcrumbs' => [
        ['name' => 'Home',         'url' => '/'],
        ['name' => 'Blog',         'url' => '/blogs'],
        ['name' => $post['title'], 'url' => '/blog/' . $slug],
    ],
    'schema' => [
        '@context'         => 'https://schema.org',
        '@type'            => 'Article',
        'headline'         => $post['title'],
        'description'      => $meta_desc,
        'image'            => $og_image ?: null,
        'author'           => ['@type' => 'Organization', 'name' => $post['author'] ?? 'General Pest Removal', 'url' => SITE_BASE_URL . '/'],
        'publisher'        => ['@type' => 'Organization', 'name' => 'General Pest Removal', 'url' => SITE_BASE_URL . '/'],
        'datePublished'    => $pub_date,
        'dateModified'     => $mod_date,
        'mainEntityOfPage' => SITE_BASE_URL . '/blog/' . $slug,
        'articleSection'   => $post['category'] ?? 'Pest Control',
    ],
]);

$related = [];
if (isset($pdo) && $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT slug, title, excerpt, category, featured_image FROM blog_posts WHERE is_published = 1 AND slug != ? AND category = ? ORDER BY published_at DESC LIMIT 3");
        $stmt->execute([$slug, $post['category']]);
        $related = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($related) < 2) {
            $stmt2 = $pdo->prepare("SELECT slug, title, excerpt, category, featured_image FROM blog_posts WHERE is_published = 1 AND slug != ? ORDER BY published_at DESC LIMIT 3");
            $stmt2->execute([$slug]);
            $related = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {}
}

require_once BASE_DIR . '/includes/header.php';
?>

<main class="flex-grow bg-gray-50">

    <!-- ── Article Hero ──────────────────────────────────────────── -->
    <section class="bg-dark text-white py-14 md:py-20 relative overflow-hidden">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb" class="mb-7">
                <ol class="flex items-center gap-2 text-xs text-gray-400 flex-wrap">
                    <li><a href="<?= $base_url ?>/" class="hover:text-white transition">Home</a></li>
                    <li aria-hidden="true" class="text-gray-600">/</li>
                    <li><a href="<?= $base_url ?>/blogs" class="hover:text-white transition">Blog</a></li>
                    <li aria-hidden="true" class="text-gray-600">/</li>
                    <li aria-current="page" class="text-white font-medium line-clamp-1"><?= htmlspecialchars($post['title']) ?></li>
                </ol>
            </nav>
            <?php if (!empty($post['category'])): ?>
            <span class="inline-block bg-secondary/20 border border-secondary/30 text-secondary text-xs font-extrabold px-4 py-1.5 rounded-full uppercase tracking-wide mb-5">
                <?= htmlspecialchars($post['category']) ?>
            </span>
            <?php endif; ?>
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight mb-5 leading-tight max-w-4xl">
                <?= htmlspecialchars($post['title']) ?>
            </h1>
            <div class="flex items-center gap-5 text-gray-400 text-xs flex-wrap">
                <span class="flex items-center gap-1.5">
                    <div class="w-6 h-6 rounded-full bg-secondary/30 flex items-center justify-center">
                        <i class="fa-solid fa-user-tie text-secondary text-xs" aria-hidden="true"></i>
                    </div>
                    <span><?= htmlspecialchars($post['author'] ?? 'General Pest Removal Team') ?></span>
                </span>
                <span class="flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                    <?= date('F j, Y', strtotime($post['published_at'])) ?>
                </span>
                <span class="flex items-center gap-1.5">
                    <i class="fa-regular fa-clock" aria-hidden="true"></i>
                    <?php
                    $word_count = str_word_count(strip_tags($post['content'] ?? $post['excerpt'] ?? ''));
                    echo max(1, (int)ceil($word_count / 200)) . ' min read';
                    ?>
                </span>
                <span class="flex items-center gap-1.5">
                    <i class="fa-solid fa-certificate text-secondary" aria-hidden="true"></i>
                    NSW Reviewed
                </span>
            </div>
        </div>
    </section>

    <!-- ── Article Body ──────────────────────────────────────────── -->
    <div class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-12">

                <!-- Main Content -->
                <article class="w-full lg:w-2/3">

                    <?php if (!empty($post['featured_image'])): ?>
                    <div class="mb-8 rounded-2xl overflow-hidden shadow-md aspect-[16/9]">
                        <img src="<?= $base_url . htmlspecialchars($post['featured_image']) ?>"
                             alt="<?= htmlspecialchars($post['title']) ?>"
                             class="w-full h-full object-cover">
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($post['excerpt'])): ?>
                    <div class="bg-yellow-50 border-l-4 border-primary rounded-r-2xl p-6 mb-8 text-gray-600 text-sm leading-relaxed italic">
                        <?= htmlspecialchars($post['excerpt']) ?>
                    </div>
                    <?php endif; ?>

                    <div class="prose max-w-none text-gray-600 leading-relaxed
                                [&_h2]:text-2xl [&_h2]:font-bold [&_h2]:text-gray-900 [&_h2]:mt-10 [&_h2]:mb-4 [&_h2]:tracking-tight
                                [&_h3]:text-xl [&_h3]:font-bold [&_h3]:text-gray-900 [&_h3]:mt-8 [&_h3]:mb-3
                                [&_p]:mb-5 [&_p]:leading-relaxed
                                [&_strong]:text-gray-900 [&_strong]:font-semibold
                                [&_a]:text-primary [&_a]:underline [&_a]:underline-offset-2 [&_a:hover]:text-secondary
                                [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:space-y-2 [&_ul]:mb-5
                                [&_ol]:list-decimal [&_ol]:pl-6 [&_ol]:space-y-2 [&_ol]:mb-5
                                [&_blockquote]:border-l-4 [&_blockquote]:border-primary/30 [&_blockquote]:pl-5 [&_blockquote]:italic [&_blockquote]:text-gray-500">
                        <?= $post['content'] ?>
                    </div>

                    <!-- In-article CTA -->
                    <div class="mt-12 bg-primary rounded-2xl p-8 text-white text-center">
                        <h2 class="text-xl font-bold tracking-tight mb-3">Ready to Solve Your Pest Problem?</h2>
                        <p class="text-gray-300 text-sm leading-relaxed mb-7">
                            Our NSW-licensed team is standing by. Free inspection, no obligation.
                        </p>
                        <div class="flex flex-wrap justify-center gap-3">
                            <a href="<?= $base_url ?>/booking"
                               class="inline-flex items-center gap-2 bg-accent hover:bg-yellow-800 text-white text-sm font-semibold px-6 py-3 rounded-lg transition">
                                Book Free Inspection
                            </a>
                            <a href="tel:<?= $site_phone_raw ?>"
                               class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/10 text-white text-sm font-semibold px-6 py-3 rounded-lg transition">
                                <i class="fa-solid fa-phone text-xs" aria-hidden="true"></i>
                                <?= $site_phone ?>
                            </a>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <a href="<?= $base_url ?>/blogs"
                           class="text-sm font-semibold text-primary hover:text-secondary transition flex items-center gap-2">
                            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                            Back to All Articles
                        </a>
                    </div>
                </article>

                <!-- Sidebar -->
                <aside class="w-full lg:w-1/3 space-y-5">
                    <div class="lg:sticky lg:top-24 space-y-5">

                        <!-- CTA Widget -->
                        <div class="bg-accent rounded-2xl p-7 text-white text-center">
                            <div class="w-14 h-14 bg-white/15 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-calendar-check text-2xl" aria-hidden="true"></i>
                            </div>
                            <h3 class="font-extrabold text-lg mb-2">Book a FREE Inspection</h3>
                            <p class="text-green-100 text-xs leading-relaxed mb-5">30-min response. No obligation. NSW licensed team.</p>
                            <a href="<?= $base_url ?>/booking"
                               class="block bg-white text-accent text-sm font-extrabold py-3.5 rounded-xl hover:bg-yellow-50 transition shadow-md mb-3">
                                Get Free Quote Now →
                            </a>
                            <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                               class="flex items-center justify-center gap-2 text-green-200 hover:text-white text-sm font-semibold transition">
                                <i class="fa-solid fa-phone text-xs" aria-hidden="true"></i>
                                <?= htmlspecialchars($site_phone) ?>
                            </a>
                        </div>

                        <!-- Services -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-4">Our Services</p>
                            <ul class="space-y-2">
                                <?php
                                $sidebar_services = [
                                    ['href' => '/services#bedbugs',     'icon' => 'fa-fire',          'color' => 'text-accent',    'bg' => 'bg-accent/10',    'label' => 'Bed Bug Heat Treatment'],
                                    ['href' => '/services#rodents',     'icon' => 'fa-cheese',         'color' => 'text-green-600','bg' => 'bg-yellow-50',    'label' => 'Rodent Control & Exclusion'],
                                    ['href' => '/services#cockroaches', 'icon' => 'fa-bug',            'color' => 'text-primary',   'bg' => 'bg-primary/10',   'label' => 'Cockroach Eradication'],
                                    ['href' => '/faq',                  'icon' => 'fa-circle-question','color' => 'text-secondary', 'bg' => 'bg-secondary/10', 'label' => 'Browse FAQ'],
                                ];
                                foreach ($sidebar_services as $ss): ?>
                                <li>
                                    <a href="<?= $base_url . $ss['href'] ?>"
                                       class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 transition group">
                                        <div class="w-8 h-8 rounded-lg <?= $ss['bg'] ?> flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid <?= $ss['icon'] ?> <?= $ss['color'] ?> text-xs" aria-hidden="true"></i>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-700 group-hover:text-primary transition"><?= $ss['label'] ?></span>
                                        <i class="fa-solid fa-arrow-right text-xs text-gray-300 ml-auto group-hover:text-primary transition" aria-hidden="true"></i>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!-- Trust / Contact -->
                        <div class="bg-primary rounded-2xl p-6 text-white">
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-4">Contact Us Directly</p>
                            <ul class="space-y-3 text-sm mb-5">
                                <li class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-lg bg-secondary flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-phone text-white text-xs" aria-hidden="true"></i>
                                    </div>
                                    <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>" class="hover:text-secondary transition font-bold"><?= htmlspecialchars($site_phone) ?></a>
                                </li>
                                <li class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-envelope text-gray-400 text-xs" aria-hidden="true"></i>
                                    </div>
                                    <a href="mailto:<?= htmlspecialchars($site_email) ?>" class="hover:text-secondary transition text-gray-400 text-xs"><?= htmlspecialchars($site_email) ?></a>
                                </li>
                                <li class="flex items-center gap-3 text-gray-400 text-xs">
                                    <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-clock text-gray-400 text-xs" aria-hidden="true"></i>
                                    </div>
                                    24/7 Emergency Line
                                </li>
                            </ul>
                            <div class="flex items-center gap-0.5">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="fa-solid fa-star text-yellow-400 text-xs" aria-hidden="true"></i>
                                <?php endfor; ?>
                                <span class="text-gray-400 text-xs ml-2">4.9/5 · 200+ Reviews</span>
                            </div>
                        </div>

                    </div>
                </aside>

            </div>

            <!-- Related Posts -->
            <?php if (!empty($related)): ?>
            <div class="mt-20 pt-12 border-t border-gray-200">
                <p class="text-xs font-semibold uppercase tracking-widest text-secondary mb-2">Keep Reading</p>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight mb-8">Related Articles</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php foreach ($related as $rel): ?>
                    <article class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden group hover:shadow-md transition-shadow">
                        <div class="aspect-[4/3] overflow-hidden">
                            <?php if (!empty($rel['featured_image'])): ?>
                            <img src="<?= $base_url . htmlspecialchars($rel['featured_image']) ?>"
                                 alt="<?= htmlspecialchars($rel['title']) ?>"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <?php else: ?>
                            <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                <i class="fa-solid fa-leaf text-3xl text-gray-300" aria-hidden="true"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-5">
                            <?php if (!empty($rel['category'])): ?>
                            <span class="text-xs font-semibold uppercase tracking-widest text-gray-400 block mb-2"><?= htmlspecialchars($rel['category']) ?></span>
                            <?php endif; ?>
                            <h3 class="font-bold text-gray-900 mb-4 group-hover:text-primary transition text-sm leading-snug line-clamp-2">
                                <?= htmlspecialchars($rel['title']) ?>
                            </h3>
                            <a href="<?= $base_url ?>/blog/<?= htmlspecialchars($rel['slug']) ?>"
                               class="text-xs font-semibold text-primary hover:text-secondary transition flex items-center gap-1.5">
                                Read Article
                                <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                            </a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php require_once BASE_DIR . '/includes/footer.php'; ?>
