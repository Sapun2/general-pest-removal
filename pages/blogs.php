<?php
require_once BASE_DIR . '/includes/db.php';
require_once BASE_DIR . '/includes/seo-meta.php';

$page_seo = get_page_seo('blogs', [
    'title'       => 'Pest Control Blog — Tips & Guides for Sydney & Brisbane | General Pest Removal',
    'description' => 'Expert pest control articles for Sydney and Brisbane homeowners and businesses. Identify termites, prevent rodent infestations, understand cockroach treatments, and learn eco-friendly IPM strategies.',
    'canonical'   => SITE_BASE_URL . '/blogs',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => '/'],
        ['name' => 'Blog', 'url' => '/blogs'],
    ],
    'schema' => [
        '@context'    => 'https://schema.org',
        '@type'       => 'Blog',
        'name'        => 'General Pest Removal Blog',
        'description' => 'Expert pest control advice for homeowners and businesses across Sydney and Brisbane.',
        'url'         => SITE_BASE_URL . '/blogs',
        'publisher'   => ['@type' => 'Organization', 'name' => 'General Pest Removal', 'url' => SITE_BASE_URL . '/'],
    ],
]);

$posts = [];
if (isset($pdo) && $pdo) {
    try {
        $posts = $pdo->query(
            "SELECT slug, title, excerpt, category, featured_image, published_at FROM blog_posts WHERE is_published = 1 ORDER BY published_at DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
}

if (empty($posts)) {
    $posts = [
        ['slug' => 'termite-inspection-sydney-brisbane',    'title' => 'Why Every Sydney & Brisbane Home Needs an Annual Termite Inspection',       'excerpt' => "Australia's warm, humid climate makes Sydney and Brisbane two of the country's highest-risk termite zones. Learn what an AS 3660.2-compliant inspection involves and how to protect your home.",    'category' => 'Termites',      'featured_image' => '/assets/images/2.png', 'published_at' => '2024-09-01'],
        ['slug' => 'signs-of-rodent-problem',       'title' => '5 Warning Signs You Have a Rodent Problem in Your Home',  'excerpt' => 'As winter approaches across Sydney and Brisbane, mice and rats seek warmth indoors. Catch the infestation early with these key warning signs before it escalates.',                   'category' => 'Rodent Control', 'featured_image' => '/assets/images/3.png', 'published_at' => '2024-09-15'],
        ['slug' => 'eradicating-german-cockroaches','title' => 'Commercial Kitchens: Eradicating German Cockroaches for Good',      'excerpt' => 'A guide for Sydney and Brisbane restaurant owners on health code compliance and why standard sprays consistently fail against resilient German cockroach populations.',              'category' => 'Commercial',     'featured_image' => '/assets/images/4.png', 'published_at' => '2024-10-01'],
        ['slug' => 'integrated-pest-management',    'title' => 'What is Integrated Pest Management (IPM) and Why Does It Matter?', 'excerpt' => 'Discover why modern pest control companies are shifting away from toxic chemical sprays toward sustainable, long-term exclusion and prevention strategies.',              'category' => 'Eco-Friendly',   'featured_image' => '',                     'published_at' => '2024-10-15'],
    ];
}

$featured = array_shift($posts);

// Estimate read time (rough: 200 words/min)
function read_time(string $text): string {
    $words = str_word_count(strip_tags($text));
    $mins  = max(1, (int)ceil($words / 200));
    return $mins . ' min read';
}

$cat_colors = [
    'Bed Bugs'      => 'bg-red-100 text-red-700',
    'Rodent Control'=> 'bg-green-100 text-green-700',
    'Commercial'    => 'bg-green-100 text-green-700',
    'Eco-Friendly'  => 'bg-green-100 text-green-700',
    'Cockroaches'   => 'bg-purple-100 text-purple-700',
];

require_once BASE_DIR . '/includes/header.php';
?>

<main class="flex-grow bg-gray-50">

    <!-- ═══════════════════════════════════════════════════════
         HERO
    ══════════════════════════════════════════════════════════ -->
    <section class="bg-dark text-white py-16 md:py-24 relative overflow-hidden">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb" class="mb-8">
                <ol class="flex items-center gap-2 text-xs text-gray-400">
                    <li><a href="<?= $base_url ?>/" class="hover:text-white transition">Home</a></li>
                    <li aria-hidden="true" class="text-gray-600">/</li>
                    <li aria-current="page" class="text-white font-medium">Blog</li>
                </ol>
            </nav>
            <div class="grid lg:grid-cols-2 gap-10 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 bg-secondary/20 border border-secondary/30 rounded-full px-4 py-1.5 mb-5">
                        <i class="fa-solid fa-newspaper text-secondary text-xs" aria-hidden="true"></i>
                        <span class="text-secondary text-xs font-semibold uppercase tracking-wide">Expert Knowledge Base</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight mb-5 leading-tight">
                        Pest Control Tips<br>
                        <span class="text-secondary">&amp; SE NSW Guides</span>
                    </h1>
                    <p class="text-gray-300 leading-relaxed text-lg mb-6">
                        Expert advice on preventing, identifying, and treating every pest problem specific to Sydney and Brisbane's unique climate and housing stock.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <?php
                        $categories = array_unique(array_column(array_merge([$featured], $posts), 'category'));
                        foreach ($categories as $cat):
                            if (empty($cat)) continue;
                            $cls = $cat_colors[$cat] ?? 'bg-white/20 text-white';
                        ?>
                        <span class="text-xs font-semibold px-3 py-1.5 rounded-full <?= $cls ?>"><?= htmlspecialchars($cat) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- Authority card -->
                <div class="bg-white/10 backdrop-blur-sm border border-white/15 rounded-2xl p-7">
                    <p class="text-gray-400 text-xs font-semibold uppercase tracking-widest mb-4">Written by Sydney Pest Experts</p>
                    <div class="space-y-4">
                        <?php
                        $authority = [
                            ['fa-certificate',  'text-secondary', 'Licensed Authors',    'All content written by or reviewed by our certified technicians.'],
                            ['fa-map-location-dot','text-secondary','Sydney-Specific Insights', 'Tips tailored for Sydney housing, climate, and pest patterns.'],
                            ['fa-shield-halved', 'text-secondary', 'Science-Based Advice',    'IPM-based guidance — not generic pest control tips.'],
                        ];
                        foreach ($authority as [$icon, $color, $title, $desc]): ?>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid <?= $icon ?> <?= $color ?> text-sm" aria-hidden="true"></i>
                            </div>
                            <div>
                                <p class="text-white font-bold text-sm leading-tight"><?= $title ?></p>
                                <p class="text-gray-400 text-xs mt-0.5"><?= $desc ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-6 pt-5 border-t border-white/10">
                        <a href="<?= $base_url ?>/booking"
                           class="block text-center bg-accent hover:bg-yellow-800 text-white font-extrabold py-3 rounded-xl transition text-sm">
                            <i class="fa-solid fa-calendar-check mr-2" aria-hidden="true"></i>
                            Book Free Inspection
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         FEATURED POST
    ══════════════════════════════════════════════════════════ -->
    <section class="py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <?php if ($featured): ?>
            <div class="mb-8">
                <p class="text-xs font-semibold uppercase tracking-widest text-secondary mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-fire" aria-hidden="true"></i>
                    Featured Article
                </p>
                <article class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col md:flex-row group hover:shadow-xl transition-shadow duration-300">
                    <div class="w-full md:w-5/12 overflow-hidden relative">
                        <?php if (!empty($featured['featured_image'])): ?>
                        <div class="aspect-[4/3] md:aspect-auto md:h-full overflow-hidden">
                            <img src="<?= $base_url . htmlspecialchars($featured['featured_image']) ?>"
                                 alt="<?= htmlspecialchars($featured['title']) ?>"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                        <?php else: ?>
                        <div class="aspect-[4/3] md:aspect-auto md:h-full bg-primary flex items-center justify-center">
                            <i class="fa-solid fa-newspaper text-5xl text-secondary" aria-hidden="true"></i>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($featured['category'])): ?>
                        <span class="absolute top-4 left-4 text-xs font-extrabold px-3 py-1.5 rounded-full <?= $cat_colors[$featured['category']] ?? 'bg-white text-gray-700' ?>">
                            <?= htmlspecialchars($featured['category']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="w-full md:w-7/12 p-8 md:p-12 flex flex-col justify-center">
                        <div class="flex items-center gap-3 mb-4 text-xs text-gray-400">
                            <?php if (!empty($featured['published_at'])): ?>
                            <span class="flex items-center gap-1">
                                <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                <?= date('F j, Y', strtotime($featured['published_at'])) ?>
                            </span>
                            <?php endif; ?>
                            <span class="flex items-center gap-1">
                                <i class="fa-regular fa-clock" aria-hidden="true"></i>
                                <?= read_time($featured['excerpt']) ?>
                            </span>
                        </div>
                        <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight mb-4 group-hover:text-primary transition leading-tight">
                            <?= htmlspecialchars($featured['title']) ?>
                        </h2>
                        <p class="text-gray-500 leading-relaxed mb-7">
                            <?= htmlspecialchars($featured['excerpt']) ?>
                        </p>
                        <div class="flex flex-wrap items-center gap-4">
                            <a href="<?= $base_url ?>/blog/<?= htmlspecialchars($featured['slug']) ?>"
                               class="inline-flex items-center gap-2 bg-primary hover:bg-yellow-900 text-white text-sm font-extrabold px-6 py-3 rounded-xl transition shadow-sm">
                                Read Full Article
                                <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                            </a>
                            <a href="<?= $base_url ?>/booking"
                               class="text-sm font-semibold text-accent hover:text-green-700 transition flex items-center gap-1.5">
                                <i class="fa-solid fa-calendar-check text-xs" aria-hidden="true"></i>
                                Book Inspection
                            </a>
                        </div>
                    </div>
                </article>
            </div>
            <?php endif; ?>

            <!-- Grid of remaining posts -->
            <?php if (!empty($posts)): ?>
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-6 flex items-center gap-2">
                <i class="fa-solid fa-newspaper" aria-hidden="true"></i>
                More Articles
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                <?php foreach ($posts as $idx => $post): ?>

                <!-- Mid-list CTA after 2nd post on larger screens -->
                <?php if ($idx === 2 && count($posts) >= 3): ?>
                <div class="bg-primary rounded-2xl p-7 text-white flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mb-4">
                            <i class="fa-solid fa-calendar-check text-secondary text-xl" aria-hidden="true"></i>
                        </div>
                        <h3 class="font-extrabold text-lg leading-tight mb-2">Ready to Solve Your Pest Problem?</h3>
                        <p class="text-gray-300 text-sm leading-relaxed mb-6">
                            Our licensed team serves Sydney &amp; Brisbane with proven results and same-day response.
                        </p>
                    </div>
                    <div class="space-y-2">
                        <a href="<?= $base_url ?>/booking"
                           class="block text-center bg-accent hover:bg-yellow-800 text-white font-extrabold py-3 rounded-xl transition text-sm">
                            Book Free Inspection →
                        </a>
                        <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                           class="flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 py-2.5 rounded-xl text-sm font-semibold transition">
                            <i class="fa-solid fa-phone text-secondary text-xs" aria-hidden="true"></i>
                            <?= htmlspecialchars($site_phone) ?>
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <article class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden group hover:shadow-lg transition-shadow duration-300 flex flex-col">
                    <div class="relative aspect-[16/9] overflow-hidden">
                        <?php if (!empty($post['featured_image'])): ?>
                        <img src="<?= $base_url . htmlspecialchars($post['featured_image']) ?>"
                             alt="<?= htmlspecialchars($post['title']) ?>"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <?php else: ?>
                        <div class="w-full h-full bg-primary flex items-center justify-center">
                            <i class="fa-solid fa-leaf text-4xl text-secondary/60" aria-hidden="true"></i>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($post['category'])): ?>
                        <span class="absolute top-3 left-3 text-xs font-extrabold px-2.5 py-1 rounded-full <?= $cat_colors[$post['category']] ?? 'bg-white text-gray-700' ?>">
                            <?= htmlspecialchars($post['category']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <div class="flex items-center gap-3 text-xs text-gray-400 mb-3">
                            <?php if (!empty($post['published_at'])): ?>
                            <span><?= date('M j, Y', strtotime($post['published_at'])) ?></span>
                            <span>·</span>
                            <?php endif; ?>
                            <span><?= read_time($post['excerpt']) ?></span>
                        </div>
                        <h3 class="text-base font-extrabold text-gray-900 mb-3 group-hover:text-primary transition leading-snug flex-grow">
                            <?= htmlspecialchars($post['title']) ?>
                        </h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-5 line-clamp-2">
                            <?= htmlspecialchars($post['excerpt']) ?>
                        </p>
                        <a href="<?= $base_url ?>/blog/<?= htmlspecialchars($post['slug']) ?>"
                           class="inline-flex items-center gap-1.5 text-sm font-extrabold text-primary hover:text-accent transition mt-auto">
                            Read Article
                            <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Bottom CTA -->
            <div class="bg-dark rounded-2xl p-10 md:p-14 text-white text-center relative overflow-hidden">
                <div class="relative">
                    <p class="text-xs font-semibold uppercase tracking-widest text-secondary mb-3">Take Action</p>
                    <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight mb-4">Stop Reading. Start Solving.</h2>
                    <p class="text-gray-400 leading-relaxed mb-8 max-w-lg mx-auto">
                        Knowledge is step one. Let our NSW-licensed Sydney team handle the rest — proven results, same-day service.
                    </p>
                    <div class="flex flex-wrap justify-center gap-3">
                        <a href="<?= $base_url ?>/booking"
                           class="inline-flex items-center gap-2 bg-accent hover:bg-yellow-800 text-white font-extrabold px-7 py-3.5 rounded-xl transition shadow-lg text-sm">
                            <i class="fa-solid fa-calendar-check" aria-hidden="true"></i>
                            Book Free Inspection
                        </a>
                        <a href="<?= $base_url ?>/services"
                           class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/10 text-white font-semibold px-7 py-3.5 rounded-xl transition text-sm">
                            View All Services
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

</main>

<?php require_once BASE_DIR . '/includes/footer.php'; ?>
