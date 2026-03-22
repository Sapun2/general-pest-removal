<?php
require_once BASE_DIR . '/includes/db.php';
require_once BASE_DIR . '/includes/seo-meta.php';

$page_seo = get_page_seo('about', [
    'title'          => 'About General Pest Removal | Licensed Pest Exterminators Sydney & Brisbane',
    'description'    => 'Meet the team behind Sydney & Brisbane\'s most trusted pest control company. Licensed, eco-friendly, and 100% satisfaction assured. Serving Sydney, Brisbane, Eastern Suburbs, Parramatta & surrounding areas.',
    'canonical'      => SITE_BASE_URL . '/about',
    'og_title'       => 'About General Pest Removal — Local Experts, Science-Based Solutions',
    'breadcrumbs'    => [
        ['name' => 'Home',     'url' => '/'],
        ['name' => 'About Us', 'url' => '/about'],
    ],
    'schema' => [
        '@context'    => 'https://schema.org',
        '@type'       => 'AboutPage',
        'name'        => 'About General Pest Removal',
        'url'         => SITE_BASE_URL . '/about',
        'about'       => [
            '@type'        => 'LocalBusiness',
            'name'         => 'General Pest Removal',
            'foundingDate' => '2009',
            'aggregateRating' => ['@type' => 'AggregateRating', 'ratingValue' => '4.8', 'reviewCount' => '186'],
        ],
    ],
]);

require_once BASE_DIR . '/includes/header.php';
?>

<main class="flex-grow bg-white">

    <!-- ═══════════════════════════════════════════════════════
         HERO
    ══════════════════════════════════════════════════════════ -->
    <section class="relative bg-dark overflow-hidden py-20 md:py-28">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb" class="mb-8">
                <ol class="flex items-center gap-2 text-xs text-gray-500">
                    <li><a href="<?= $base_url ?>/" class="hover:text-gray-300 transition">Home</a></li>
                    <li aria-hidden="true" class="text-gray-700">/</li>
                    <li aria-current="page" class="text-gray-300 font-medium">About Us</li>
                </ol>
            </nav>

            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 bg-secondary/15 border border-secondary/25 rounded-full px-4 py-1.5 mb-6">
                        <i class="fa-solid fa-calendar-days text-secondary text-xs" aria-hidden="true"></i>
                        <span class="text-secondary text-xs font-semibold uppercase tracking-wide">Serving Sydney &amp; Brisbane Since 2009</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-white tracking-tight leading-tight mb-5">
                        Australia's Most Trusted<br>
                        <span class="text-secondary">Pest Control Team</span>
                    </h1>
                    <p class="text-gray-300 text-lg leading-relaxed mb-8 max-w-lg">
                        We've built our reputation one home at a time — through certified expertise, eco-friendly methods, and a no-excuses satisfaction assurance that we actually stand behind.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="<?= $base_url ?>/booking"
                           class="inline-flex items-center gap-2 bg-accent hover:bg-yellow-800 text-white font-extrabold px-6 py-3 rounded-xl transition shadow-lg text-sm">
                            <i class="fa-solid fa-calendar-check" aria-hidden="true"></i>
                            Book Free Inspection
                        </a>
                        <a href="<?= $base_url ?>/services"
                           class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/15 text-white font-semibold px-6 py-3 rounded-xl transition text-sm">
                            Our Services
                        </a>
                    </div>
                </div>

                <!-- Quick stats grid -->
                <div class="grid grid-cols-2 gap-4">
                    <?php
                    $hero_stats = [
                        ['num' => '15+',  'label' => 'Years in Business',         'icon' => 'fa-calendar-days', 'color' => 'text-secondary'],
                        ['num' => '500+', 'label' => 'Homes & Businesses Served', 'icon' => 'fa-house-circle-check','color' => 'text-secondary'],
                        ['num' => '4.9★', 'label' => 'Average Google Rating',     'icon' => 'fa-star',          'color' => 'text-yellow-400'],
                        ['num' => '100%', 'label' => '100% Satisfaction',         'icon' => 'fa-shield-halved', 'color' => 'text-secondary'],
                    ];
                    foreach ($hero_stats as $s): ?>
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-5 backdrop-blur-sm">
                        <i class="fa-solid <?= $s['icon'] ?> <?= $s['color'] ?> text-xl mb-3 block" aria-hidden="true"></i>
                        <p class="text-3xl font-extrabold text-white leading-none mb-1"><?= $s['num'] ?></p>
                        <p class="text-xs text-gray-400 leading-snug"><?= $s['label'] ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         CREDENTIALS / TRUST STRIP
    ══════════════════════════════════════════════════════════ -->
    <div class="bg-white border-b border-gray-100 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-center md:justify-between gap-6 text-center">
                <?php
                $creds = [
                    ['fa-id-card-clip',  'primary',   'Licensed',        'NSW & QLD Certified'],
                    ['fa-shield-halved', 'secondary', 'Fully Insured',         '$5M+ Liability Coverage'],
                    ['fa-leaf',          'secondary', 'Eco-Friendly Methods',  'Safe for Kids & Pets'],
                    ['fa-bolt',          'accent',    '2-Hour Response',       'Business Hours Commitment'],
                    ['fa-star',          'yellow-500','4.9 Google Rating',     '200+ Verified Reviews'],
                ];
                foreach ($creds as [$icon, $color, $title, $sub]): ?>
                <div class="flex items-center gap-3 min-w-[160px]">
                    <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid <?= $icon ?> text-<?= $color ?>" aria-hidden="true"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-bold text-gray-900 leading-tight"><?= $title ?></p>
                        <p class="text-xs text-gray-400"><?= $sub ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════
         OUR STORY / MISSION
    ══════════════════════════════════════════════════════════ -->
    <section class="py-20 md:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">

                <!-- Image + floating badge -->
                <div class="relative order-2 lg:order-1">
                    <div class="rounded-2xl overflow-hidden shadow-2xl aspect-[4/3]">
                        <img src="<?= $base_url ?>/assets/images/5.png"
                             alt="General Pest Removal licensed technicians at work"
                             class="w-full h-full object-cover">
                    </div>
                    <!-- Floating founded badge -->
                    <div class="absolute -bottom-5 -right-5 bg-dark border border-white/10 text-white px-6 py-5 rounded-2xl shadow-2xl hidden md:block">
                        <p class="text-4xl font-extrabold text-secondary leading-none">2009</p>
                        <p class="text-sm text-gray-400 mt-1">Founded in Australia</p>
                    </div>
                    <!-- Floating Google rating -->
                    <div class="absolute -top-4 -left-4 bg-white border border-gray-100 shadow-xl rounded-2xl px-4 py-3 hidden md:flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center flex-shrink-0">
                            <i class="fa-brands fa-google text-white text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-0.5">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="fa-solid fa-star text-yellow-400 text-xs" aria-hidden="true"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="text-xs font-bold text-gray-900 mt-0.5">4.9 · 200+ Reviews</p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="order-1 lg:order-2">
                    <p class="text-xs font-semibold uppercase tracking-widest text-secondary mb-3">Our Story</p>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight mb-6 leading-tight">
                        Built on Science,<br>Driven by Results
                    </h2>
                    <div class="space-y-4 text-gray-500 leading-relaxed text-[15px] mb-8">
                        <p>Sydney and Brisbane present pest challenges unlike anywhere else in Australia. Dense apartment towers where bed bugs travel through shared walls. Aging housing stock with countless rodent entry points. Multi-unit commercial buildings where cockroach colonies spread through shared plumbing.</p>
                        <p>We were founded in 2009 to address these challenges with precision across Australia's two largest east coast cities. From day one, we committed to <strong class="text-gray-800">Integrated Pest Management (IPM)</strong> — identifying root causes, sealing entry points permanently, and using the safest effective treatments available.</p>
                        <p>Today, every one of our technicians holds a valid <strong class="text-gray-800">pest management licence</strong>. We carry $5M+ in liability insurance. And we back every treatment with a written service report.</p>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <?php
                        $mission_points = [
                            'No subcontractors — every technician is a trained General Pest Removal employee',
                            'Unmarked vehicles for complete discretion in your neighbourhood',
                            'Written service reports and treatment assurances on every job',
                            'Chemical-free thermal options always available on request',
                        ];
                        foreach ($mission_points as $pt): ?>
                        <li class="flex items-start gap-3 text-sm text-gray-700">
                            <div class="w-5 h-5 rounded-full bg-secondary/15 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-check text-secondary text-xs" aria-hidden="true"></i>
                            </div>
                            <?= $pt ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?= $base_url ?>/services" class="inline-flex items-center gap-2 text-primary font-bold text-sm hover:text-secondary transition">
                        See Our Full Service List
                        <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         OUR APPROACH — 4 steps
    ══════════════════════════════════════════════════════════ -->
    <section class="py-20 md:py-28 bg-gray-50" aria-labelledby="process-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <p class="text-xs font-semibold uppercase tracking-widest text-secondary mb-3">How We Work</p>
                <h2 id="process-heading" class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight mb-4">
                    Our IPM-Based Approach
                </h2>
                <p class="text-gray-500 leading-relaxed">
                    Integrated Pest Management means we treat the cause, not just the symptom. This is why our results last.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php
                $process = [
                    ['step' => '01', 'icon' => 'fa-magnifying-glass', 'color' => 'bg-primary',   'title' => 'Thorough Inspection',    'desc' => 'We identify every harborage area, entry point, and contributing condition — not just the visible pest activity.'],
                    ['step' => '02', 'icon' => 'fa-clipboard-list',   'color' => 'bg-secondary',  'title' => 'Custom Treatment Plan',  'desc' => 'We design a targeted protocol specific to your pest, property type, and family needs. No one-size-fits-all approaches.'],
                    ['step' => '03', 'icon' => 'fa-spray-can-sparkles','color' => 'bg-accent',    'title' => 'Precision Treatment',    'desc' => 'Heat, gel bait, exclusion, or mechanical control — we use the right method for complete, lasting elimination.'],
                    ['step' => '04', 'icon' => 'fa-rotate-left',      'color' => 'bg-dark',       'title' => 'Protection & Follow-Up', 'desc' => 'Written service report on every job. If pests return within the service period, we come back at absolutely no charge.'],
                ];
                foreach ($process as $p): ?>
                <div class="bg-white rounded-2xl border border-gray-100 p-7 shadow-sm hover:shadow-md transition-shadow text-center">
                    <div class="w-14 h-14 <?= $p['color'] ?> rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-md">
                        <i class="fa-solid <?= $p['icon'] ?> text-white text-xl" aria-hidden="true"></i>
                    </div>
                    <span class="text-xs font-extrabold text-gray-200 uppercase tracking-widest block mb-2"><?= $p['step'] ?></span>
                    <h3 class="text-base font-extrabold text-gray-900 mb-2"><?= $p['title'] ?></h3>
                    <p class="text-sm text-gray-500 leading-relaxed"><?= $p['desc'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         TRUST / WHY CHOOSE US — Detailed cards
    ══════════════════════════════════════════════════════════ -->
    <section class="py-20 md:py-28 bg-white" aria-labelledby="trust-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <p class="text-xs font-semibold uppercase tracking-widest text-secondary mb-3">Why Sydney &amp; Brisbane Homeowners Choose Us</p>
                <h2 id="trust-heading" class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">
                    What Sets Us Apart
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php
                $trust = [
                    [
                        'icon'  => 'fa-id-card-clip',
                        'bg'    => 'bg-primary/10',
                        'color' => 'text-primary',
                        'title' => 'Licensed & $5M Insured',
                        'desc'  => 'Every technician holds a valid pest management licence. We carry comprehensive $5M+ liability insurance on every service call across NSW and QLD — so you\'re fully protected.',
                        'badge' => 'Regulatory Compliance',
                    ],
                    [
                        'icon'  => 'fa-leaf',
                        'bg'    => 'bg-secondary/10',
                        'color' => 'text-secondary',
                        'title' => 'Genuinely Eco-Friendly',
                        'desc'  => 'We offer 100% chemical-free thermal heat treatments for bed bugs. When chemical applications are required, we use only APVMA-approved low-toxicity products with specific re-entry guidelines to keep your family safe.',
                        'badge' => 'Environmental Safety',
                    ],
                    [
                        'icon'  => 'fa-truck-fast',
                        'bg'    => 'bg-accent/10',
                        'color' => 'text-accent',
                        'title' => 'Same-Day, 24/7 Emergency',
                        'desc'  => 'Pest problems can\'t wait. We offer easy online booking with 30-minute call-back response during business hours. For genuine emergencies — aggressive wildlife, severe infestations — our 24/7 line connects you to a real technician.',
                        'badge' => 'Response Speed',
                    ],
                ];
                foreach ($trust as $t): ?>
                <div class="bg-gray-50 rounded-2xl border border-gray-100 p-8 hover:shadow-md transition-shadow relative overflow-hidden">
                    <span class="absolute top-4 right-4 text-xs font-semibold text-gray-300 uppercase tracking-widest"><?= $t['badge'] ?></span>
                    <div class="w-14 h-14 rounded-2xl <?= $t['bg'] ?> <?= $t['color'] ?> flex items-center justify-center mb-6">
                        <i class="fa-solid <?= $t['icon'] ?> text-xl" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-extrabold text-gray-900 mb-3"><?= $t['title'] ?></h3>
                    <p class="text-gray-500 text-sm leading-relaxed"><?= $t['desc'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         STATS — Dark section
    ══════════════════════════════════════════════════════════ -->
    <section class="bg-dark py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-white/5">
                <?php
                $stats = [
                    ['num' => '2009',  'label' => 'Year Founded',              'icon' => 'fa-flag',          'color' => 'text-secondary'],
                    ['num' => '500+',  'label' => 'Homes & Businesses Served', 'icon' => 'fa-house',         'color' => 'text-secondary'],
                    ['num' => '25+',   'label' => 'Suburbs Covered',  'icon' => 'fa-map-location-dot','color' => 'text-secondary'],
                    ['num' => '24/7',  'label' => 'Emergency Availability',    'icon' => 'fa-phone-volume',  'color' => 'text-accent'],
                ];
                foreach ($stats as $s): ?>
                <div class="bg-dark text-center py-10 px-6">
                    <i class="fa-solid <?= $s['icon'] ?> <?= $s['color'] ?> text-2xl mb-4 block" aria-hidden="true"></i>
                    <p class="text-4xl font-extrabold text-white mb-2 leading-none"><?= $s['num'] ?></p>
                    <p class="text-sm text-gray-400"><?= $s['label'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         TESTIMONIALS
    ══════════════════════════════════════════════════════════ -->
    <section class="py-20 md:py-28 bg-gray-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <p class="text-xs font-semibold uppercase tracking-widest text-secondary mb-3">What Clients Say</p>
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Trusted by Homeowners Across Sydney &amp; Brisbane</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php
                $reviews = [
                    ['name' => 'Jennifer L.', 'area' => 'Sydney CBD', 'service' => 'Bed Bug Heat Treatment',
                     'text' => 'After dealing with bed bugs for two months and trying two other companies, General Pest Removal resolved the problem in a single heat treatment visit. The technician was thorough, professional, and explained every step. I\'ve been recommending them to everyone in my condo building.'],
                    ['name' => 'Robert C.',   'area' => 'Eastern Suburbs',      'service' => 'Rodent Exclusion',
                     'text' => 'They found eight separate entry points that other exterminators missed over three years. Every gap was sealed properly with steel wool and caulk. It\'s been seven months and not a single sign of mice. The seasonal contract gives me year-round peace of mind.'],
                ];
                foreach ($reviews as $r): ?>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                    <div class="flex items-center gap-0.5 mb-5">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                        <i class="fa-solid fa-star text-yellow-400" aria-hidden="true"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="text-gray-600 leading-relaxed mb-6 text-[15px]">"<?= $r['text'] ?>"</p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center flex-shrink-0">
                                <span class="text-white font-extrabold text-sm"><?= substr($r['name'], 0, 1) ?></span>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 text-sm"><?= $r['name'] ?></p>
                                <p class="text-xs text-gray-400"><?= $r['area'] ?></p>
                            </div>
                        </div>
                        <span class="text-xs font-semibold bg-secondary/10 text-secondary px-3 py-1 rounded-full"><?= $r['service'] ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         CTA BANNER
    ══════════════════════════════════════════════════════════ -->
    <section class="bg-accent py-16 md:py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-extrabold text-white tracking-tight mb-4">
                Ready to Work with Sydney &amp; Brisbane's Best?
            </h2>
            <p class="text-green-100 mb-8 max-w-xl mx-auto">
                Get a free inspection and quote from our licensed team — same-day response, proven results.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="<?= $base_url ?>/booking"
                   class="inline-flex items-center gap-2.5 bg-white text-accent hover:bg-yellow-50 font-extrabold px-8 py-4 rounded-xl transition shadow-xl text-sm">
                    <i class="fa-solid fa-calendar-check" aria-hidden="true"></i>
                    Book Free Inspection
                </a>
                <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                   class="inline-flex items-center gap-2 bg-dark/30 hover:bg-dark/50 border border-white/20 text-white font-bold px-8 py-4 rounded-xl transition text-sm">
                    <i class="fa-solid fa-phone text-sm" aria-hidden="true"></i>
                    <?= htmlspecialchars($site_phone) ?>
                </a>
            </div>
            <div class="flex items-center justify-center gap-6 mt-6">
                <span class="text-green-200 text-xs flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i> No obligation
                </span>
                <span class="text-green-200 text-xs flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i> 100% free quote
                </span>
                <span class="text-green-200 text-xs flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i> Licensed team
                </span>
            </div>
        </div>
    </section>

</main>

<?php require_once BASE_DIR . '/includes/footer.php'; ?>
