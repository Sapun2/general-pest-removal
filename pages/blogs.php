<?php
require_once BASE_DIR . '/includes/db.php';
require_once BASE_DIR . '/includes/seo-meta.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$flash_error = $_SESSION['flash_error'] ?? '';
$form_data   = $_SESSION['form_data']   ?? [];
unset($_SESSION['flash_error'], $_SESSION['form_data']);

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
        ['slug' => 'termite-inspection-sydney-brisbane',    'title' => 'Why Every Sydney & Brisbane Home Needs an Annual Termite Inspection',       'excerpt' => "Australia's warm, humid climate makes Sydney and Brisbane two of the country's highest-risk termite zones. Learn what an AS 3660.2-compliant inspection involves and how to protect your home.",    'category' => 'Termites',       'featured_image' => '/assets/images/2.png', 'published_at' => '2024-09-01'],
        ['slug' => 'signs-of-rodent-problem',               'title' => '5 Warning Signs You Have a Rodent Problem in Your Home',                    'excerpt' => 'As winter approaches across Sydney and Brisbane, mice and rats seek warmth indoors. Catch the infestation early with these key warning signs before it escalates.',                   'category' => 'Rodent Control', 'featured_image' => '/assets/images/3.png', 'published_at' => '2024-09-15'],
        ['slug' => 'eradicating-german-cockroaches',        'title' => 'Commercial Kitchens: Eradicating German Cockroaches for Good',              'excerpt' => 'A guide for Sydney and Brisbane restaurant owners on health code compliance and why standard sprays consistently fail against resilient German cockroach populations.',              'category' => 'Commercial',     'featured_image' => '/assets/images/4.png', 'published_at' => '2024-10-01'],
        ['slug' => 'integrated-pest-management',            'title' => 'What is Integrated Pest Management (IPM) and Why Does It Matter?',         'excerpt' => 'Discover why modern pest control companies are shifting away from toxic chemical sprays toward sustainable, long-term exclusion and prevention strategies.',              'category' => 'Eco-Friendly',   'featured_image' => '',                     'published_at' => '2024-10-15'],
    ];
}

$featured = array_shift($posts);

function read_time(string $text): string {
    $words = str_word_count(strip_tags($text));
    $mins  = max(1, (int)ceil($words / 200));
    return $mins . ' min read';
}

$cat_colors = [
    'Termites'      => 'bg-green-100 text-green-700 border-green-200',
    'Rodent Control'=> 'bg-slate-100 text-slate-600 border-slate-200',
    'Commercial'    => 'bg-blue-50 text-blue-700 border-blue-100',
    'Eco-Friendly'  => 'bg-emerald-50 text-emerald-700 border-emerald-100',
    'Cockroaches'   => 'bg-slate-100 text-slate-600 border-slate-200',
];

require_once BASE_DIR . '/includes/header.php';
?>

<main class="flex-grow bg-white">

    <!-- ── HERO ─────────────────────────────────────────────────── -->
    <section class="bg-dark text-white py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb" class="mb-8">
                <ol class="flex items-center gap-2 text-xs text-slate-500">
                    <li><a href="<?= $base_url ?>/" class="hover:text-slate-300 transition">Home</a></li>
                    <li aria-hidden="true" class="text-slate-700">/</li>
                    <li aria-current="page" class="text-slate-300">Blog</li>
                </ol>
            </nav>
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 bg-green-600/15 border border-green-500/20 rounded-full px-4 py-1.5 mb-6">
                        <i class="fa-solid fa-newspaper text-green-400 text-xs" aria-hidden="true"></i>
                        <span class="text-green-400 text-xs font-semibold uppercase tracking-wide">Expert Knowledge Base</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight mb-5 leading-tight">
                        Pest Control Tips<br>
                        <span class="text-green-400">&amp; Expert Guides</span>
                    </h1>
                    <p class="text-slate-300 leading-relaxed mb-8 max-w-lg text-lg">
                        Expert advice on preventing, identifying, and treating every pest problem specific to Sydney and Brisbane's unique climate.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <?php
                        $badges = ['2-Hour Response', 'Free Retreatment', 'Pet & Child Safe', '4.9★ Rated'];
                        foreach ($badges as $badge): ?>
                        <span class="inline-flex items-center gap-1.5 bg-white/8 border border-white/12 text-slate-300 text-xs font-medium px-3 py-1.5 rounded-full">
                            <i class="fa-solid fa-check text-green-400 text-[10px]" aria-hidden="true"></i>
                            <?= $badge ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Quick Quote Form -->
                <div class="bg-white rounded-xl p-6 shadow-xl">
                    <p class="font-bold text-slate-900 text-base mb-1">Get a Free Quote</p>
                    <p class="text-slate-500 text-xs mb-5">We call back within 2 hours during business hours.</p>

                    <?php if ($flash_error): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 text-xs px-3 py-2 rounded-lg mb-4">
                        <?= htmlspecialchars($flash_error) ?>
                    </div>
                    <?php endif; ?>

                    <form action="<?= $base_url ?>/process_booking" method="POST" class="space-y-3" novalidate data-crm="booking">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                        <div class="grid grid-cols-2 gap-3">
                            <input type="text" name="first_name" required placeholder="First Name *"
                                   value="<?= htmlspecialchars($form_data['first_name'] ?? '') ?>"
                                   class="w-full px-3.5 py-3 rounded-lg border border-slate-200 bg-slate-50 text-slate-900 text-sm focus:bg-white focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/10 transition placeholder:text-slate-400">
                            <input type="text" name="last_name" required placeholder="Last Name *"
                                   value="<?= htmlspecialchars($form_data['last_name'] ?? '') ?>"
                                   class="w-full px-3.5 py-3 rounded-lg border border-slate-200 bg-slate-50 text-slate-900 text-sm focus:bg-white focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/10 transition placeholder:text-slate-400">
                        </div>
                        <input type="tel" name="phone" required placeholder="Phone Number *"
                               value="<?= htmlspecialchars($form_data['phone'] ?? '') ?>"
                               class="w-full px-3.5 py-3 rounded-lg border border-slate-200 bg-slate-50 text-slate-900 text-sm focus:bg-white focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/10 transition placeholder:text-slate-400">
                        <input type="text" name="street_address" required placeholder="Street Address *"
                               value="<?= htmlspecialchars($form_data['street_address'] ?? '') ?>"
                               class="w-full px-3.5 py-3 rounded-lg border border-slate-200 bg-slate-50 text-slate-900 text-sm focus:bg-white focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/10 transition placeholder:text-slate-400">
                        <div class="relative">
                            <select name="pest_type" required
                                    class="w-full appearance-none px-3.5 py-3 rounded-lg border border-slate-200 bg-slate-50 text-slate-900 text-sm focus:bg-white focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/10 transition pr-10">
                                <option value="">What's the pest?</option>
                                <option value="termites">Termites</option>
                                <option value="cockroaches">Cockroaches</option>
                                <option value="spiders">Spiders</option>
                                <option value="ants">Ants / Fire Ants</option>
                                <option value="rodents">Mice / Rats</option>
                                <option value="wasps">Wasps / Bees</option>
                                <option value="other">Other / Not Sure</option>
                            </select>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                <i class="fa-solid fa-chevron-down text-xs" aria-hidden="true"></i>
                            </span>
                        </div>
                        <button type="submit"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3.5 rounded-lg transition flex items-center justify-center gap-2 text-sm">
                            <i class="fa-solid fa-calendar-check" aria-hidden="true"></i>
                            Get Free Quote
                        </button>
                        <p class="text-center text-xs text-slate-400">
                            <i class="fa-solid fa-lock mr-1" aria-hidden="true"></i>No obligation · 100% confidential
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- ── BLOG CONTENT ──────────────────────────────────────────── -->
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Featured Post -->
            <?php if ($featured): ?>
            <div class="mb-14">
                <p class="text-xs font-semibold uppercase tracking-widest text-green-600 mb-6">Featured Article</p>
                <a href="<?= $base_url ?>/blog/<?= htmlspecialchars($featured['slug']) ?>"
                   class="group block bg-white border border-slate-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow md:flex">
                    <?php if (!empty($featured['featured_image'])): ?>
                    <div class="md:w-2/5 aspect-[16/9] md:aspect-auto overflow-hidden flex-shrink-0">
                        <img src="<?= $base_url . htmlspecialchars($featured['featured_image']) ?>"
                             alt="<?= htmlspecialchars($featured['title']) ?>"
                             class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-500"
                             loading="lazy">
                    </div>
                    <?php endif; ?>
                    <div class="p-7 md:p-9 flex flex-col justify-center">
                        <div class="flex items-center gap-3 mb-4">
                            <?php
                            $feat_cls = $cat_colors[$featured['category']] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                            ?>
                            <span class="text-xs font-semibold border px-2.5 py-1 rounded-full <?= $feat_cls ?>">
                                <?= htmlspecialchars($featured['category']) ?>
                            </span>
                            <span class="text-xs text-slate-400">
                                <?= date('d M Y', strtotime($featured['published_at'])) ?>
                                · <?= read_time($featured['excerpt']) ?>
                            </span>
                        </div>
                        <h2 class="text-xl md:text-2xl font-bold text-slate-900 mb-3 group-hover:text-green-700 transition leading-snug">
                            <?= htmlspecialchars($featured['title']) ?>
                        </h2>
                        <p class="text-slate-500 text-sm leading-relaxed mb-5">
                            <?= htmlspecialchars($featured['excerpt']) ?>
                        </p>
                        <span class="inline-flex items-center gap-2 text-green-600 font-semibold text-sm">
                            Read Article
                            <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                        </span>
                    </div>
                </a>
            </div>
            <?php endif; ?>

            <!-- Post Grid -->
            <?php if (!empty($posts)): ?>
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-green-600 mb-6">Latest Articles</p>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($posts as $post): ?>
                    <a href="<?= $base_url ?>/blog/<?= htmlspecialchars($post['slug']) ?>"
                       class="group block bg-white border border-slate-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow flex flex-col">
                        <?php if (!empty($post['featured_image'])): ?>
                        <div class="aspect-[16/9] overflow-hidden flex-shrink-0">
                            <img src="<?= $base_url . htmlspecialchars($post['featured_image']) ?>"
                                 alt="<?= htmlspecialchars($post['title']) ?>"
                                 class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500"
                                 loading="lazy">
                        </div>
                        <?php else: ?>
                        <div class="aspect-[16/9] bg-slate-100 border-b border-slate-200 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-newspaper text-slate-300 text-4xl" aria-hidden="true"></i>
                        </div>
                        <?php endif; ?>
                        <div class="p-5 flex flex-col flex-grow">
                            <div class="flex items-center gap-3 mb-3">
                                <?php
                                $cls = $cat_colors[$post['category']] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                                ?>
                                <span class="text-xs font-semibold border px-2.5 py-1 rounded-full <?= $cls ?>">
                                    <?= htmlspecialchars($post['category']) ?>
                                </span>
                                <span class="text-xs text-slate-400"><?= read_time($post['excerpt']) ?></span>
                            </div>
                            <h3 class="font-bold text-slate-900 mb-2 leading-snug text-sm group-hover:text-green-700 transition flex-grow">
                                <?= htmlspecialchars($post['title']) ?>
                            </h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4">
                                <?= mb_strimwidth(htmlspecialchars($post['excerpt']), 0, 120, '...') ?>
                            </p>
                            <span class="inline-flex items-center gap-1.5 text-green-600 font-semibold text-xs mt-auto">
                                Read More <i class="fa-solid fa-arrow-right text-[10px]" aria-hidden="true"></i>
                            </span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Mid CTA -->
            <div class="mt-14 bg-dark rounded-xl border border-white/8 p-8 md:p-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-green-400 mb-2">Act Now</p>
                    <h3 class="text-xl font-bold text-white mb-1">Have a pest problem right now?</h3>
                    <p class="text-slate-400 text-sm">Don't wait — book a free inspection today. We respond within 2 hours.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 flex-shrink-0">
                    <a href="<?= $base_url ?>/booking"
                       class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition text-sm whitespace-nowrap">
                        <i class="fa-regular fa-calendar-check" aria-hidden="true"></i>
                        Book Free Inspection
                    </a>
                    <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                       class="inline-flex items-center gap-2 border border-white/15 hover:border-white/25 text-white font-medium px-6 py-3 rounded-lg transition text-sm whitespace-nowrap">
                        <i class="fa-solid fa-phone text-xs text-green-400" aria-hidden="true"></i>
                        <?= htmlspecialchars($site_phone) ?>
                    </a>
                </div>
            </div>

        </div>
    </section>

</main>

<?php require_once BASE_DIR . '/includes/footer.php'; ?>
