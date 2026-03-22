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
            'aggregateRating' => ['@type' => 'AggregateRating', 'ratingValue' => '4.9', 'reviewCount' => '200'],
        ],
    ],
]);

require_once BASE_DIR . '/includes/header.php';
?>

<main class="flex-grow bg-white">

    <!-- ── HERO ─────────────────────────────────────────────────── -->
    <section class="bg-dark py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb" class="mb-8">
                <ol class="flex items-center gap-2 text-xs text-slate-500">
                    <li><a href="<?= $base_url ?>/" class="hover:text-slate-300 transition">Home</a></li>
                    <li aria-hidden="true" class="text-slate-700">/</li>
                    <li aria-current="page" class="text-slate-300">About Us</li>
                </ol>
            </nav>

            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 bg-green-600/15 border border-green-500/20 rounded-full px-4 py-1.5 mb-6">
                        <i class="fa-solid fa-calendar-days text-green-400 text-xs" aria-hidden="true"></i>
                        <span class="text-green-400 text-xs font-semibold uppercase tracking-wide">Serving Sydney &amp; Brisbane Since 2009</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white tracking-tight leading-tight mb-5">
                        Australia's Most Trusted<br>
                        <span class="text-green-400">Pest Control Team</span>
                    </h1>
                    <p class="text-slate-300 text-lg leading-relaxed mb-8 max-w-lg">
                        We've built our reputation one home at a time — through certified expertise, eco-friendly methods, and a no-excuses satisfaction assurance we actually stand behind.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="<?= $base_url ?>/booking"
                           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition text-sm">
                            <i class="fa-solid fa-calendar-check" aria-hidden="true"></i>
                            Book Free Inspection
                        </a>
                        <a href="<?= $base_url ?>/services"
                           class="inline-flex items-center gap-2 border border-white/15 hover:border-white/25 text-white font-medium px-6 py-3 rounded-lg transition text-sm">
                            Our Services
                        </a>
                    </div>
                </div>

                <!-- Stats grid -->
                <div class="grid grid-cols-2 gap-4">
                    <?php
                    $hero_stats = [
                        ['15+',  'Years in Business',         'fa-calendar-days'],
                        ['500+', 'Homes & Businesses Served', 'fa-house-circle-check'],
                        ['4.9★', 'Average Google Rating',     'fa-star'],
                        ['100%', 'Satisfaction Assured',      'fa-shield-halved'],
                    ];
                    foreach ($hero_stats as [$num, $label, $icon]): ?>
                    <div class="bg-white/5 border border-white/8 rounded-xl p-5">
                        <i class="fa-solid <?= $icon ?> text-green-400 text-xl mb-3 block" aria-hidden="true"></i>
                        <p class="text-3xl font-bold text-white leading-none mb-1"><?= $num ?></p>
                        <p class="text-xs text-slate-400 leading-snug"><?= $label ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ── CREDENTIALS STRIP ─────────────────────────────────────── -->
    <div class="bg-white border-b border-slate-100 py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-center gap-6 lg:gap-10">
                <?php
                $creds = [
                    ['fa-id-card-clip',  'Licensed',              'NSW &amp; QLD Certified'],
                    ['fa-shield-halved', 'Fully Insured',         '$5M+ Liability Coverage'],
                    ['fa-leaf',          'Eco-Friendly Methods',  'Safe for Kids &amp; Pets'],
                    ['fa-bolt',          '2-Hour Response',       'Business Hours Commitment'],
                    ['fa-star',          '4.9 Google Rating',     '200+ Verified Reviews'],
                ];
                foreach ($creds as [$icon, $title, $sub]): ?>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid <?= $icon ?> text-green-600 text-sm" aria-hidden="true"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900 leading-tight"><?= $title ?></p>
                        <p class="text-xs text-slate-400"><?= $sub ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ── OUR STORY ─────────────────────────────────────────────── -->
    <section class="py-20 md:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">

                <!-- Image -->
                <div class="relative">
                    <div class="rounded-xl overflow-hidden aspect-[4/3]">
                        <img src="<?= $base_url ?>/assets/images/5.png"
                             alt="General Pest Removal licensed technicians at work"
                             class="w-full h-full object-cover">
                    </div>
                    <div class="absolute -bottom-4 -right-4 bg-dark border border-white/10 text-white px-5 py-4 rounded-xl shadow-xl hidden md:block">
                        <p class="text-3xl font-bold text-green-400 leading-none">2009</p>
                        <p class="text-sm text-slate-400 mt-1">Founded in Australia</p>
                    </div>
                    <div class="absolute -top-4 -left-4 bg-white border border-slate-200 shadow-xl rounded-xl px-4 py-3 hidden md:flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center flex-shrink-0">
                            <i class="fa-brands fa-google text-white text-xs" aria-hidden="true"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-0.5">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="fa-solid fa-star text-yellow-400 text-xs" aria-hidden="true"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="text-xs font-bold text-slate-900 mt-0.5">4.9 · 200+ Reviews</p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-green-600 mb-3">Our Story</p>
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 tracking-tight mb-6 leading-tight">
                        Built on Science,<br>Driven by Results
                    </h2>
                    <div class="space-y-4 text-slate-500 leading-relaxed text-[15px] mb-8">
                        <p>Sydney and Brisbane present pest challenges unlike anywhere else in Australia. Dense apartment towers where bed bugs travel through shared walls. Aging housing stock with countless rodent entry points. Multi-unit commercial buildings where cockroach colonies spread through shared plumbing.</p>
                        <p>We were founded in 2009 to address these challenges with precision. From day one, we committed to <strong class="text-slate-800">Integrated Pest Management (IPM)</strong> — identifying root causes, sealing entry points permanently, and using the safest effective treatments available.</p>
                        <p>Today, every technician holds a valid <strong class="text-slate-800">pest management licence</strong>. We carry $5M+ in liability insurance. And we back every treatment with a written service report.</p>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <?php
                        $points = [
                            'No subcontractors — every technician is a trained employee',
                            'Unmarked vehicles for complete discretion',
                            'Written service reports on every job',
                            'Chemical-free thermal options always available',
                        ];
                        foreach ($points as $pt): ?>
                        <li class="flex items-start gap-3 text-sm text-slate-700">
                            <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-check text-green-600 text-[10px]" aria-hidden="true"></i>
                            </div>
                            <?= $pt ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?= $base_url ?>/services"
                       class="inline-flex items-center gap-2 text-green-600 font-semibold text-sm hover:text-green-700 transition">
                        See Our Full Service List
                        <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ── OUR APPROACH ──────────────────────────────────────────── -->
    <section class="py-20 md:py-28 bg-slate-50" aria-labelledby="process-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <p class="text-xs font-semibold uppercase tracking-widest text-green-600 mb-3">How We Work</p>
                <h2 id="process-heading" class="text-3xl md:text-4xl font-bold text-slate-900 tracking-tight mb-4">
                    Our 4-Step IPM Approach
                </h2>
                <p class="text-slate-500 text-sm leading-relaxed">
                    We don't just spray and leave. Our Integrated Pest Management process finds the root cause and prevents re-infestation.
                </p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php
                $ipm = [
                    ['fa-magnifying-glass', '01', 'Thorough Inspection',     'We identify the pest species, extent of infestation, and all entry points before recommending any treatment.'],
                    ['fa-flask',            '02', 'Targeted Treatment',      'We select the lowest-risk, most effective APVMA-registered treatment for your specific pest and environment.'],
                    ['fa-hammer',           '03', 'Exclusion & Prevention',  'We seal entry points, remove harborage conditions, and advise on environmental changes to prevent re-infestation.'],
                    ['fa-file-contract',    '04', 'Written Report & Assurance', 'Every job comes with a detailed service report and a written retreatment assurance for your peace of mind.'],
                ];
                foreach ($ipm as [$icon, $num, $title, $desc]): ?>
                <div class="bg-white rounded-xl border border-slate-200 p-6">
                    <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center mb-4">
                        <i class="fa-solid <?= $icon ?> text-white text-sm" aria-hidden="true"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-300 uppercase tracking-widest mb-2">Step <?= $num ?></p>
                    <h3 class="font-bold text-slate-900 mb-2 text-sm"><?= $title ?></h3>
                    <p class="text-sm text-slate-500 leading-relaxed"><?= $desc ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ── WHAT SETS US APART ─────────────────────────────────────── -->
    <section class="py-20 md:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-xl mx-auto mb-14">
                <p class="text-xs font-semibold uppercase tracking-widest text-green-600 mb-3">Our Difference</p>
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 tracking-tight">
                    What Sets Us Apart
                </h2>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <?php
                $diff = [
                    ['fa-user-tie',     'Employee Technicians Only',      'We never use subcontractors. Every technician is a trained, background-checked General Pest Removal employee — accountable to our standards every single job.'],
                    ['fa-leaf',         'Science-First, Chemical-Last',    'We exhaust non-chemical options first. When products are necessary, we choose the safest, most targeted APVMA-registered formulations available.'],
                    ['fa-rotate-left',  'Genuine Retreatment Assurance',  'Not a marketing gimmick — a written guarantee. If pests return within your service period, we come back and retreat your property at no charge.'],
                ];
                foreach ($diff as [$icon, $title, $desc]): ?>
                <div class="border border-slate-200 rounded-xl p-7">
                    <div class="w-12 h-12 bg-green-50 border border-green-100 rounded-lg flex items-center justify-center mb-5">
                        <i class="fa-solid <?= $icon ?> text-green-600" aria-hidden="true"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-3"><?= $title ?></h3>
                    <p class="text-sm text-slate-500 leading-relaxed"><?= $desc ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ── STATS — dark ──────────────────────────────────────────── -->
    <section class="py-16 md:py-20 bg-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 text-center">
                <?php
                $about_stats = [
                    ['15+',  'Years Serving Sydney &amp; Brisbane'],
                    ['500+', 'Properties Treated'],
                    ['4.9★', 'Average Google Rating'],
                    ['100%', 'Satisfaction Assured'],
                ];
                foreach ($about_stats as [$num, $label]): ?>
                <div class="py-6">
                    <p class="text-4xl font-bold text-white leading-none mb-2"><?= $num ?></p>
                    <p class="text-sm text-slate-400"><?= $label ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ── TESTIMONIALS ──────────────────────────────────────────── -->
    <section class="py-20 md:py-28 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <p class="text-xs font-semibold uppercase tracking-widest text-green-600 mb-3">Customer Reviews</p>
                <h2 class="text-2xl md:text-3xl font-bold text-slate-900 tracking-tight">What Our Customers Say</h2>
            </div>

            <div class="grid md:grid-cols-2 gap-6 max-w-4xl mx-auto">
                <?php
                $reviews = [
                    ['name' => 'Michael L.', 'area' => 'North Shore, NSW', 'service' => 'Termite Inspection',
                     'review' => 'The thermal imaging inspection was non-invasive and found activity in a wall cavity I would never have suspected. The written AS 3660.2 report was exactly what my bank required for refinancing. Highly professional.'],
                    ['name' => 'Jessica R.', 'area' => 'Southside Brisbane', 'service' => 'Cockroach Treatment',
                     'review' => 'Our restaurant kitchen was getting cockroach complaints and the standard sprays weren\'t working. General Pest Removal\'s gel bait approach eliminated the problem completely within a week. Full health code compliance since.'],
                ];
                foreach ($reviews as $r): ?>
                <div class="bg-white border border-slate-200 rounded-xl p-7">
                    <div class="flex items-center gap-0.5 mb-4">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                        <i class="fa-solid fa-star text-yellow-400 text-xs" aria-hidden="true"></i>
                        <?php endfor; ?>
                    </div>
                    <blockquote class="text-slate-600 text-sm leading-relaxed mb-5">"<?= $r['review'] ?>"</blockquote>
                    <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                        <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold text-xs"><?= substr($r['name'], 0, 1) ?></span>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900 text-sm"><?= $r['name'] ?></p>
                            <p class="text-xs text-slate-400"><?= $r['area'] ?> · <?= $r['service'] ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ── CTA ───────────────────────────────────────────────────── -->
    <section class="py-16 md:py-20 bg-dark border-t border-white/5">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white tracking-tight mb-4">
                Ready to Get Started?
            </h2>
            <p class="text-slate-300 text-lg mb-8">
                Book a free inspection and speak with a licensed technician today.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="<?= $base_url ?>/booking"
                   class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-7 py-3.5 rounded-lg transition text-sm">
                    <i class="fa-regular fa-calendar-check" aria-hidden="true"></i>
                    Book Free Inspection
                </a>
                <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                   class="inline-flex items-center gap-2 border border-white/15 hover:border-white/25 text-white font-medium px-7 py-3.5 rounded-lg transition text-sm">
                    <i class="fa-solid fa-phone text-xs text-green-400" aria-hidden="true"></i>
                    <?= htmlspecialchars($site_phone) ?>
                </a>
            </div>
        </div>
    </section>

</main>

<?php require_once BASE_DIR . '/includes/footer.php'; ?>
