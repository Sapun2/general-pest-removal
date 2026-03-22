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
                <div class="w-16 h-16 bg-slate-100 rounded-xl flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-newspaper text-2xl text-slate-400" aria-hidden="true"></i>
                </div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight mb-4">Article Not Found</h1>
                <p class="text-slate-500 mb-8">This post may have been moved or does not exist yet.</p>
                <div class="flex flex-wrap justify-center gap-3">
                    <a href="' . $base_url . '/blogs" class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-6 py-3 rounded-lg transition">View All Articles</a>
                    <a href="' . $base_url . '/booking" class="border border-slate-200 text-slate-700 text-sm font-semibold px-6 py-3 rounded-lg hover:bg-slate-50 transition">Book Inspection</a>
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

<main class="flex-grow bg-white">

    <!-- ── Article Hero ──────────────────────────────────────────── -->
    <section class="bg-dark text-white py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb" class="mb-7">
                <ol class="flex items-center gap-2 text-xs text-slate-500 flex-wrap">
                    <li><a href="<?= $base_url ?>/" class="hover:text-slate-300 transition">Home</a></li>
                    <li aria-hidden="true" class="text-slate-700">/</li>
                    <li><a href="<?= $base_url ?>/blogs" class="hover:text-slate-300 transition">Blog</a></li>
                    <li aria-hidden="true" class="text-slate-700">/</li>
                    <li aria-current="page" class="text-slate-300 line-clamp-1"><?= htmlspecialchars($post['title']) ?></li>
                </ol>
            </nav>
            <?php if (!empty($post['category'])): ?>
            <span class="inline-flex items-center bg-green-600/15 border border-green-500/20 text-green-400 text-xs font-semibold px-3 py-1.5 rounded-full uppercase tracking-wide mb-5">
                <?= htmlspecialchars($post['category']) ?>
            </span>
            <?php endif; ?>
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight mb-6 leading-tight max-w-4xl">
                <?= htmlspecialchars($post['title']) ?>
            </h1>
            <div class="flex items-center gap-5 text-slate-400 text-xs flex-wrap">
                <span class="flex items-center gap-1.5">
                    <i class="fa-regular fa-user text-xs" aria-hidden="true"></i>
                    <?= htmlspecialchars($post['author'] ?? 'General Pest Removal Team') ?>
                </span>
                <span class="flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar text-xs" aria-hidden="true"></i>
                    <?= date('F j, Y', strtotime($post['published_at'])) ?>
                </span>
                <span class="flex items-center gap-1.5">
                    <i class="fa-regular fa-clock text-xs" aria-hidden="true"></i>
                    <?php
                    $word_count = str_word_count(strip_tags($post['content'] ?? $post['excerpt'] ?? ''));
                    echo max(1, (int)ceil($word_count / 200)) . ' min read';
                    ?>
                </span>
                <span class="flex items-center gap-1.5">
                    <i class="fa-solid fa-certificate text-green-400 text-xs" aria-hidden="true"></i>
                    NSW Reviewed
                </span>
            </div>
        </div>
    </section>

    <!-- ── Article Body ──────────────────────────────────────────── -->
    <div class="py-14 md:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-12">

                <!-- Main Content -->
                <article class="w-full lg:w-2/3">

                    <?php if (!empty($post['featured_image'])): ?>
                    <div class="mb-8 rounded-xl overflow-hidden aspect-[16/9]">
                        <img src="<?= $base_url . htmlspecialchars($post['featured_image']) ?>"
                             alt="<?= htmlspecialchars($post['title']) ?>"
                             class="w-full h-full object-cover">
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($post['excerpt'])): ?>
                    <div class="border-l-4 border-green-600 bg-green-50 rounded-r-xl p-5 mb-8 text-slate-600 text-sm leading-relaxed italic">
                        <?= htmlspecialchars($post['excerpt']) ?>
                    </div>
                    <?php endif; ?>

                    <div class="prose max-w-none text-slate-600 leading-relaxed
                                [&_h2]:text-2xl [&_h2]:font-bold [&_h2]:text-slate-900 [&_h2]:mt-10 [&_h2]:mb-4 [&_h2]:tracking-tight
                                [&_h3]:text-xl [&_h3]:font-bold [&_h3]:text-slate-900 [&_h3]:mt-8 [&_h3]:mb-3
                                [&_p]:mb-5 [&_p]:leading-relaxed
                                [&_strong]:text-slate-900 [&_strong]:font-semibold
                                [&_a]:text-green-600 [&_a]:underline [&_a]:underline-offset-2 [&_a:hover]:text-green-700
                                [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:space-y-2 [&_ul]:mb-5
                                [&_ol]:list-decimal [&_ol]:pl-6 [&_ol]:space-y-2 [&_ol]:mb-5
                                [&_blockquote]:border-l-4 [&_blockquote]:border-green-300 [&_blockquote]:pl-5 [&_blockquote]:italic [&_blockquote]:text-slate-500">
                        <?= $post['content'] ?>
                    </div>

                    <!-- In-article CTA -->
                    <div class="mt-12 bg-dark rounded-xl border border-white/8 p-8 text-center">
                        <h2 class="text-xl font-bold text-white tracking-tight mb-3">Ready to Solve Your Pest Problem?</h2>
                        <p class="text-slate-400 text-sm leading-relaxed mb-6">
                            Our NSW-licensed team is standing by. Free inspection, no obligation.
                        </p>
                        <div class="flex flex-wrap justify-center gap-3">
                            <a href="<?= $base_url ?>/booking"
                               class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-6 py-3 rounded-lg transition">
                                <i class="fa-regular fa-calendar-check" aria-hidden="true"></i>
                                Book Free Inspection
                            </a>
                            <a href="tel:<?= $site_phone_raw ?>"
                               class="inline-flex items-center gap-2 border border-white/15 hover:border-white/25 text-white text-sm font-medium px-6 py-3 rounded-lg transition">
                                <i class="fa-solid fa-phone text-green-400 text-xs" aria-hidden="true"></i>
                                <?= $site_phone ?>
                            </a>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100">
                        <a href="<?= $base_url ?>/blogs"
                           class="text-sm font-semibold text-green-600 hover:text-green-700 transition flex items-center gap-2">
                            <i class="fa-solid fa-arrow-left text-xs" aria-hidden="true"></i>
                            Back to All Articles
                        </a>
                    </div>
                </article>

                <!-- Sidebar -->
                <aside class="w-full lg:w-1/3">
                    <div class="lg:sticky lg:top-24 space-y-5">

                        <!-- CTA Widget -->
                        <div class="bg-dark rounded-xl border border-white/8 p-7 text-white">
                            <div class="w-12 h-12 bg-green-600/20 rounded-lg flex items-center justify-center mb-4">
                                <i class="fa-solid fa-calendar-check text-green-400 text-xl" aria-hidden="true"></i>
                            </div>
                            <h3 class="font-bold text-base mb-2">Book a FREE Inspection</h3>
                            <p class="text-slate-400 text-xs leading-relaxed mb-5">2-hour response. No obligation. Licensed team.</p>
                            <a href="<?= $base_url ?>/booking"
                               class="block text-center bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-3 rounded-lg transition mb-3">
                                Get Free Quote
                            </a>
                            <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                               class="flex items-center justify-center gap-2 text-slate-400 hover:text-white text-sm transition">
                                <i class="fa-solid fa-phone text-green-400 text-xs" aria-hidden="true"></i>
                                <?= htmlspecialchars($site_phone) ?>
                            </a>
                        </div>

                        <!-- Services -->
                        <div class="bg-white rounded-xl border border-slate-200 p-6">
                            <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-4">Our Services</p>
                            <ul class="space-y-2">
                                <?php
                                $sidebar_services = [
                                    ['/services#termites',    'fa-house-crack',   'Termite Inspection'],
                                    ['/services#cockroaches', 'fa-bug',           'Cockroach Control'],
                                    ['/services#rodents',     'fa-cheese',        'Rodent Control'],
                                    ['/faq',                  'fa-circle-question','Browse FAQ'],
                                ];
                                foreach ($sidebar_services as [$href, $icon, $label]): ?>
                                <li>
                                    <a href="<?= $base_url . $href ?>"
                                       class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-slate-50 transition group">
                                        <div class="w-8 h-8 rounded-lg bg-green-50 border border-green-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid <?= $icon ?> text-green-600 text-xs" aria-hidden="true"></i>
                                        </div>
                                        <span class="text-sm font-medium text-slate-700 group-hover:text-green-700 transition"><?= $label ?></span>
                                        <i class="fa-solid fa-arrow-right text-xs text-slate-300 ml-auto group-hover:text-green-600 transition" aria-hidden="true"></i>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!-- Contact info -->
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-6">
                            <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-4">Contact Us</p>
                            <ul class="space-y-3 text-sm mb-4">
                                <li class="flex items-center gap-3">
                                    <i class="fa-solid fa-phone text-green-600 text-xs w-4" aria-hidden="true"></i>
                                    <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>" class="hover:text-green-700 transition font-semibold text-slate-900"><?= htmlspecialchars($site_phone) ?></a>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i class="fa-solid fa-envelope text-slate-400 text-xs w-4" aria-hidden="true"></i>
                                    <a href="mailto:<?= htmlspecialchars($site_email) ?>" class="hover:text-green-700 transition text-slate-500 text-xs break-all"><?= htmlspecialchars($site_email) ?></a>
                                </li>
                                <li class="flex items-center gap-3 text-slate-400 text-xs">
                                    <i class="fa-solid fa-clock text-slate-400 text-xs w-4" aria-hidden="true"></i>
                                    24/7 Emergency Line
                                </li>
                            </ul>
                            <div class="flex items-center gap-0.5">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="fa-solid fa-star text-yellow-400 text-xs" aria-hidden="true"></i>
                                <?php endfor; ?>
                                <span class="text-slate-400 text-xs ml-2">4.9/5 · 200+ Reviews</span>
                            </div>
                        </div>

                    </div>
                </aside>

            </div>

            <!-- Related Posts -->
            <?php if (!empty($related)): ?>
            <div class="mt-20 pt-12 border-t border-slate-200">
                <p class="text-xs font-semibold uppercase tracking-widest text-green-600 mb-2">Keep Reading</p>
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight mb-8">Related Articles</h2>
                <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6">
                    <?php foreach ($related as $rel): ?>
                    <a href="<?= $base_url ?>/blog/<?= htmlspecialchars($rel['slug']) ?>"
                       class="group block bg-white border border-slate-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow">
                        <?php if (!empty($rel['featured_image'])): ?>
                        <div class="aspect-[4/3] overflow-hidden">
                            <img src="<?= $base_url . htmlspecialchars($rel['featured_image']) ?>"
                                 alt="<?= htmlspecialchars($rel['title']) ?>"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                        <?php else: ?>
                        <div class="aspect-[4/3] bg-slate-100 flex items-center justify-center">
                            <i class="fa-solid fa-leaf text-3xl text-slate-300" aria-hidden="true"></i>
                        </div>
                        <?php endif; ?>
                        <div class="p-5">
                            <?php if (!empty($rel['category'])): ?>
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest block mb-2"><?= htmlspecialchars($rel['category']) ?></span>
                            <?php endif; ?>
                            <h3 class="font-semibold text-slate-900 text-sm leading-snug group-hover:text-green-700 transition line-clamp-2">
                                <?= htmlspecialchars($rel['title']) ?>
                            </h3>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php require_once BASE_DIR . '/includes/footer.php'; ?>
