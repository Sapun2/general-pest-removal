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

// ── Load services from DB with hardcoded fallback ───────────────
$services_data = [];
if (isset($pdo) && $pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
        $services_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($services_data as &$svc) {
            $svc['features'] = json_decode($svc['features'] ?? '[]', true) ?: [];
        }
        unset($svc);
    } catch (PDOException $e) {
        $services_data = [];
    }
}

if (empty($services_data)) {
    $services_data = [
        [
            'id'          => 1,
            'slug'        => 'termites',
            'name'        => 'Termite Inspection & Treatment',
            'tagline'     => 'Thermal imaging inspections. Proven barrier treatments.',
            'description' => '<p>Sydney and Brisbane have some of the highest termite activity rates in Australia — with subtropical climates creating year-round risk. Subterranean termites cause billions in property damage annually and most home insurance policies do not cover it. Our licensed inspectors use thermal imaging cameras and moisture meters to detect activity without drilling or destructive testing.</p>',
            'icon'        => 'fa-house-crack',
            'badge_text'  => 'Most Critical Service',
            'image_path'  => '/assets/images/2.png',
            'link_anchor' => '#termites',
            'sort_order'  => 1,
            'is_active'   => 1,
            'features'    => [
                ['icon' => 'fa-camera',        'title' => 'Thermal Imaging Inspection',   'desc' => 'Non-invasive detection using FLIR thermal cameras — no drilling, no damage to your property.'],
                ['icon' => 'fa-shield-halved', 'title' => 'Reticulation Barrier Systems', 'desc' => 'Continuous chemical barrier systems replenished on-demand to protect your full perimeter.'],
                ['icon' => 'fa-file-contract', 'title' => 'AS 3660.2-Compliant Reports',  'desc' => 'Full written inspection reports required by lenders and conveyancers for NSW and QLD property transactions.'],
            ],
        ],
        [
            'id'          => 2,
            'slug'        => 'cockroaches',
            'name'        => 'Cockroach Control',
            'tagline'     => 'Colony-level elimination — not just the ones you see.',
            'description' => '<p>Sydney\'s and Brisbane\'s warm, humid climates make them ideal breeding grounds for German and American cockroaches year-round. Over-the-counter sprays scatter colonies without eliminating them. Our IPM-based approach targets harborage zones with pharmaceutical-grade gel baits that the entire colony ingests, eliminating the source in a single treatment cycle.</p>',
            'icon'        => 'fa-bug',
            'badge_text'  => '#1 Australian Pest Problem',
            'image_path'  => '/assets/images/3.png',
            'link_anchor' => '#cockroaches',
            'sort_order'  => 2,
            'is_active'   => 1,
            'features'    => [
                ['icon' => 'fa-droplet',   'title' => 'Gel Bait Treatments',     'desc' => 'Slow-acting colony-level baits applied to harborage zones in kitchens and bathrooms.'],
                ['icon' => 'fa-spray-can', 'title' => 'Residual Surface Sprays', 'desc' => 'Long-lasting residual products applied to skirtings, subfloor voids, and entry points.'],
                ['icon' => 'fa-leaf',      'title' => 'Pet & Family Safe',        'desc' => 'Low-toxicity APVMA-registered formulations with standard 2–4 hour re-entry periods.'],
            ],
        ],
        [
            'id'          => 3,
            'slug'        => 'spiders',
            'name'        => 'Spider Removal',
            'tagline'     => 'Redbacks, Funnel-webs & White-tails — safely eliminated.',
            'description' => '<p>Sydney and Brisbane are home to some of Australia\'s most venomous spider species including Redback Spiders, Eastern Funnel-web Spiders, and White-tailed Spiders. We provide comprehensive spider removal and preventive webbing spray treatments that create a long-lasting barrier around your home\'s exterior, eaves, and entry points.</p>',
            'icon'        => 'fa-spider',
            'badge_text'  => 'Family Safe Methods',
            'image_path'  => '/assets/images/4.png',
            'link_anchor' => '#spiders',
            'sort_order'  => 3,
            'is_active'   => 1,
            'features'    => [
                ['icon' => 'fa-house',         'title' => 'Exterior Webbing Treatment',  'desc' => 'Full perimeter spray targeting eaves, window frames, garden areas, and entry points.'],
                ['icon' => 'fa-magnifying-glass','title' => 'Harborage Identification',  'desc' => 'Locate and treat common hiding areas including garden beds, timber piles, and letterboxes.'],
                ['icon' => 'fa-child-reaching', 'title' => 'Child & Pet Safe Options',   'desc' => 'Synthetic pyrethroid formulas with short re-entry times and no residual odour.'],
            ],
        ],
        [
            'id'          => 4,
            'slug'        => 'ants',
            'name'        => 'Ant Treatment',
            'tagline'     => 'Fire ants, coastal brown ants & more — source eliminated.',
            'description' => '<p>South East Queensland (including Brisbane) and parts of NSW are ground zero for the national Fire Ant eradication program. Beyond the invasive fire ant, homes across Sydney and Brisbane regularly deal with Coastal Brown Ants, Black Garden Ants, and Carpenter Ants. We identify the species accurately before treatment — because the wrong product on the wrong ant makes infestations worse.</p>',
            'icon'        => 'fa-circle-dot',
            'badge_text'  => 'High Priority Pest',
            'image_path'  => '/assets/images/2.png',
            'link_anchor' => '#ants',
            'sort_order'  => 4,
            'is_active'   => 1,
            'features'    => [
                ['icon' => 'fa-magnifying-glass', 'title' => 'Species Identification',  'desc' => 'Correct species ID before treatment — critical for effective and safe outcomes.'],
                ['icon' => 'fa-circle-dot',       'title' => 'Granular & Liquid Baits', 'desc' => 'Slow-transfer baits carried back to the colony, eliminating the queen and reproductives.'],
                ['icon' => 'fa-border-all',       'title' => 'Perimeter Barrier',       'desc' => 'Residual perimeter treatment preventing re-entry from surrounding garden areas.'],
            ],
        ],
        [
            'id'          => 5,
            'slug'        => 'rodents',
            'name'        => 'Rodent Control',
            'tagline'     => 'Rats & mice — eliminated and excluded permanently.',
            'description' => '<p>Rapid urban growth and construction across Sydney and Brisbane constantly displaces rat and mouse populations into suburban homes and commercial kitchens. We don\'t just bait — we conduct a full entry-point audit and seal every gap, crack, and pipe penetration with rodent-proof materials so they cannot return.</p>',
            'icon'        => 'fa-cheese',
            'badge_text'  => 'Year-Round Problem',
            'image_path'  => '/assets/images/3.png',
            'link_anchor' => '#rodents',
            'sort_order'  => 5,
            'is_active'   => 1,
            'features'    => [
                ['icon' => 'fa-hammer',   'title' => 'Full Exclusion Sealing',   'desc' => 'Steel wool, wire mesh, and expanding foam applied to all entry points and pipe penetrations.'],
                ['icon' => 'fa-cheese',   'title' => 'Tamper-Resistant Baiting', 'desc' => 'Secure bait stations in key pathways for rapid colony reduction.'],
                ['icon' => 'fa-calendar', 'title' => 'Ongoing Monitoring Plans', 'desc' => 'Regular inspections and bait replenishment for commercial and high-risk residential properties.'],
            ],
        ],
    ];
}

$page_seo = get_page_seo('services', [
    'title'          => 'Pest Removal Services in Sydney & Brisbane | Termites, Cockroaches, Spiders',
    'description'    => 'Professional termite inspections, cockroach control, spider removal, ant treatment and rodent control across Sydney & Brisbane. Licensed & insured. Book online today.',
    'canonical'      => SITE_BASE_URL . '/services',
    'og_title'       => 'General Pest Removal Services — Termites, Cockroaches, Spiders & More',
    'breadcrumbs'    => [
        ['name' => 'Home',     'url' => '/'],
        ['name' => 'Services', 'url' => '/services'],
    ],
]);

require_once BASE_DIR . '/includes/header.php';
?>

<main class="flex-grow bg-white">

    <!-- ═══════════════════════════════════════════════════════
         HERO — Service overview + quick navigation
    ══════════════════════════════════════════════════════════ -->
    <section class="bg-dark text-white py-16 md:py-24 relative overflow-hidden">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb" class="mb-8">
                <ol class="flex items-center gap-2 text-xs text-gray-400">
                    <li><a href="<?= $base_url ?>/" class="hover:text-white transition">Home</a></li>
                    <li aria-hidden="true" class="text-gray-600">/</li>
                    <li aria-current="page" class="text-white font-medium">Services</li>
                </ol>
            </nav>

            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 bg-secondary/20 border border-secondary/30 rounded-full px-4 py-1.5 mb-6">
                        <i class="fa-solid fa-certificate text-secondary text-xs" aria-hidden="true"></i>
                        <span class="text-secondary text-xs font-semibold uppercase tracking-wide">Licensed · Fully Insured</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight mb-5 leading-tight">
                        Pest Removal Services<br>
                        <span class="text-secondary">Across Sydney &amp; Brisbane</span>
                    </h1>
                    <p class="text-gray-300 leading-relaxed mb-8 max-w-lg text-lg">
                        Science-based, eco-friendly, and proven pest elimination for residential and commercial properties. We respond within 2 hours.
                    </p>

                    <!-- Trust pills -->
                    <div class="flex flex-wrap gap-3 mb-10">
                        <?php
                        $badges = [
                            ['fa-bolt',         '2-Hour Response'],
                            ['fa-rotate-left',  'Free Retreatment'],
                            ['fa-leaf',         'Pet & Child Safe'],
                            ['fa-star',         '4.9★ Rated'],
                        ];
                        foreach ($badges as [$icon, $label]): ?>
                        <span class="inline-flex items-center gap-1.5 bg-white/10 border border-white/15 text-white text-xs font-medium px-3 py-1.5 rounded-full">
                            <i class="fa-solid <?= $icon ?> text-secondary text-xs" aria-hidden="true"></i>
                            <?= $label ?>
                        </span>
                        <?php endforeach; ?>
                    </div>

                    <!-- CTA row -->
                    <div class="flex flex-wrap gap-3">
                        <a href="<?= $base_url ?>/booking"
                           class="inline-flex items-center gap-2 bg-accent hover:bg-yellow-800 text-white font-bold px-6 py-3 rounded-xl transition text-sm shadow-lg">
                            <i class="fa-solid fa-calendar-check text-xs" aria-hidden="true"></i>
                            Book Free Inspection
                        </a>
                        <a href="#pricing"
                           class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/15 text-white font-medium px-6 py-3 rounded-xl transition text-sm">
                            <i class="fa-solid fa-tag text-xs" aria-hidden="true"></i>
                            View Pricing
                        </a>
                    </div>
                </div>

                <!-- Quick Quote Box -->
                <div class="bg-white/10 backdrop-blur-sm border border-white/15 rounded-2xl p-6">
                    <p class="text-white font-extrabold text-lg mb-1">Get a Free Quote Now</p>
                    <p class="text-gray-400 text-xs mb-5">Fill in your details — we call back within 2 hours.</p>

                    <?php if ($flash_error): ?>
                    <div class="bg-red-900/50 border border-red-400/30 text-red-200 text-xs px-3 py-2 rounded-lg mb-4">
                        <?= htmlspecialchars($flash_error) ?>
                    </div>
                    <?php endif; ?>

                    <form action="<?= $base_url ?>/process_booking" method="POST" class="space-y-3" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                        <div class="grid grid-cols-2 gap-3">
                            <input type="text" name="first_name" required placeholder="First Name *"
                                   value="<?= htmlspecialchars($form_data['first_name'] ?? '') ?>"
                                   class="w-full px-3 py-2.5 rounded-lg border border-white/20 bg-white/10 text-white placeholder-green-300 text-sm outline-none focus:border-secondary focus:bg-white/15 transition">
                            <input type="text" name="last_name" required placeholder="Last Name *"
                                   value="<?= htmlspecialchars($form_data['last_name'] ?? '') ?>"
                                   class="w-full px-3 py-2.5 rounded-lg border border-white/20 bg-white/10 text-white placeholder-green-300 text-sm outline-none focus:border-secondary focus:bg-white/15 transition">
                        </div>
                        <input type="tel" name="phone" required placeholder="Phone Number *"
                               value="<?= htmlspecialchars($form_data['phone'] ?? '') ?>"
                               class="w-full px-3 py-2.5 rounded-lg border border-white/20 bg-white/10 text-white placeholder-green-300 text-sm outline-none focus:border-secondary focus:bg-white/15 transition">
                        <input type="text" name="street_address" required placeholder="Street Address *"
                               value="<?= htmlspecialchars($form_data['street_address'] ?? '') ?>"
                               class="w-full px-3 py-2.5 rounded-lg border border-white/20 bg-white/10 text-white placeholder-green-300 text-sm outline-none focus:border-secondary focus:bg-white/15 transition">
                        <select name="pest_type" required
                                class="w-full px-3 py-2.5 rounded-lg border border-white/20 bg-white/10 text-white text-sm outline-none focus:border-secondary focus:bg-white/15 transition">
                            <option value="" class="text-gray-900">What's the pest?</option>
                            <option value="termites"    class="text-gray-900">Termites</option>
                            <option value="cockroaches" class="text-gray-900">Cockroaches</option>
                            <option value="spiders"     class="text-gray-900">Spiders</option>
                            <option value="ants"        class="text-gray-900">Ants / Fire Ants</option>
                            <option value="rodents"     class="text-gray-900">Mice / Rats</option>
                            <option value="wasps"       class="text-gray-900">Wasps / Bees</option>
                            <option value="other"       class="text-gray-900">Other / Not Sure</option>
                        </select>
                        <button type="submit"
                                class="w-full bg-accent hover:bg-yellow-800 text-white font-extrabold py-3.5 rounded-xl transition shadow-lg flex items-center justify-center gap-2 text-sm">
                            <i class="fa-solid fa-calendar-check" aria-hidden="true"></i>
                            Get FREE Quote →
                        </button>
                        <p class="text-center text-xs text-gray-400">
                            <i class="fa-solid fa-lock mr-1" aria-hidden="true"></i>No obligation · 100% confidential
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         SERVICES CARD GRID
    ══════════════════════════════════════════════════════════ -->
    <section class="py-16 md:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-xl mx-auto mb-10">
                <p class="text-xs font-semibold uppercase tracking-widest text-secondary mb-2">What We Treat</p>
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">
                    All Services — Book Any Online
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php
                $guarantee_map = [0 => '30-Day', 1 => '90-Day', 2 => '6-Month', 3 => '90-Day', 4 => '6-Month'];
                foreach ($services_data as $idx => $svc):
                    $slug      = htmlspecialchars($svc['slug']);
                    $icon      = htmlspecialchars($svc['icon'] ?? 'fa-bug');
                    $guarantee = ($guarantee_map[$idx] ?? '30-Day') . ' Protection';
                    $features  = array_slice($svc['features'] ?? [], 0, 3);
                ?>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg transition-shadow flex flex-col overflow-hidden group">

                    <!-- Image -->
                    <?php if (!empty($svc['image_path'])): ?>
                    <div class="relative h-44 overflow-hidden flex-shrink-0">
                        <img src="<?= $base_url . htmlspecialchars($svc['image_path']) ?>"
                             alt="<?= htmlspecialchars($svc['name']) ?>"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-dark/60 to-transparent"></div>
                        <!-- Icon + badge overlaid on image -->
                        <div class="absolute bottom-3 left-4 flex items-center gap-2">
                            <div class="w-9 h-9 rounded-lg bg-white/20 backdrop-blur-sm border border-white/30 flex items-center justify-center">
                                <i class="fa-solid <?= $icon ?> text-white text-sm" aria-hidden="true"></i>
                            </div>
                        </div>
                        <?php if (!empty($svc['badge_text'])): ?>
                        <div class="absolute top-3 right-3">
                            <span class="text-[10px] font-extrabold uppercase tracking-wide text-white bg-accent px-2.5 py-1 rounded-full">
                                <?= htmlspecialchars($svc['badge_text']) ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <!-- Fallback when no image -->
                    <div class="h-20 bg-primary/8 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid <?= $icon ?> text-primary text-3xl" aria-hidden="true"></i>
                    </div>
                    <?php endif; ?>

                    <!-- Name + tagline -->
                    <div class="px-5 pt-4 pb-3">
                        <h3 class="font-extrabold text-gray-900 leading-tight text-[15px] mb-1">
                            <?= htmlspecialchars($svc['name']) ?>
                        </h3>
                        <?php if (!empty($svc['tagline'])): ?>
                        <p class="text-xs text-gray-500 leading-snug">
                            <?= htmlspecialchars($svc['tagline']) ?>
                        </p>
                        <?php endif; ?>
                    </div>

                    <!-- Feature bullets -->
                    <?php if (!empty($features)): ?>
                    <ul class="px-5 pb-4 space-y-1.5 flex-grow">
                        <?php foreach ($features as $feat): ?>
                        <li class="flex items-start gap-2 text-xs text-gray-600">
                            <i class="fa-solid fa-check text-secondary text-[10px] flex-shrink-0 mt-1" aria-hidden="true"></i>
                            <span><?= htmlspecialchars($feat['title']) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>

                    <!-- Footer -->
                    <div class="px-5 pb-5 pt-3 border-t border-gray-50 mt-auto flex items-center justify-between gap-3">
                        <span class="text-xs text-gray-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-shield-halved text-secondary text-[10px]" aria-hidden="true"></i>
                            <?= $guarantee ?>
                        </span>
                        <a href="<?= $base_url ?>/booking?service=<?= $slug ?>"
                           class="inline-flex items-center gap-1.5 bg-accent hover:bg-yellow-800 text-white text-xs font-extrabold px-4 py-2.5 rounded-lg transition shadow-sm whitespace-nowrap">
                            <i class="fa-regular fa-calendar-check text-[10px]" aria-hidden="true"></i>
                            Book Now
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Catch-all Book card -->
                <div class="bg-dark rounded-2xl border border-white/5 shadow-sm flex flex-col items-center justify-center text-center p-8 gap-4">
                    <div class="w-14 h-14 bg-accent/20 rounded-2xl flex items-center justify-center">
                        <i class="fa-solid fa-circle-question text-accent text-2xl" aria-hidden="true"></i>
                    </div>
                    <div>
                        <p class="font-extrabold text-white text-base mb-1">Not sure?</p>
                        <p class="text-gray-400 text-xs leading-relaxed">Tell us what you're seeing — we'll identify and treat it.</p>
                    </div>
                    <a href="<?= $base_url ?>/booking"
                       class="inline-flex items-center gap-2 bg-accent hover:bg-yellow-800 text-white text-sm font-extrabold px-6 py-3 rounded-xl transition shadow-lg w-full justify-center">
                        <i class="fa-regular fa-calendar-check" aria-hidden="true"></i>
                        Get Free Quote
                    </a>
                    <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                       class="text-xs text-gray-400 hover:text-white transition flex items-center gap-1.5">
                        <i class="fa-solid fa-phone text-[10px]" aria-hidden="true"></i>
                        <?= htmlspecialchars($site_phone) ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         SOCIAL PROOF STRIP
    ══════════════════════════════════════════════════════════ -->
    <section class="bg-dark py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-white/10">
                <?php
                $stats = [
                    ['num' => '500+',  'label' => 'Jobs Completed',        'icon' => 'fa-briefcase'],
                    ['num' => '4.9★',  'label' => 'Google Rating',         'icon' => 'fa-star'],
                    ['num' => '30min', 'label' => 'Avg. Response Time',    'icon' => 'fa-bolt'],
                    ['num' => '100%',  'label' => '100% Satisfaction',     'icon' => 'fa-shield-halved'],
                ];
                foreach ($stats as $s): ?>
                <div class="bg-dark text-center py-8 px-4">
                    <i class="fa-solid <?= $s['icon'] ?> text-secondary text-xl mb-3 block" aria-hidden="true"></i>
                    <p class="text-3xl font-extrabold text-white mb-1"><?= $s['num'] ?></p>
                    <p class="text-gray-400 text-sm"><?= $s['label'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         PRICING PLANS
    ══════════════════════════════════════════════════════════ -->
    <section id="pricing" class="py-20 md:py-28 bg-gray-50 scroll-mt-20" aria-labelledby="pricing-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <p class="text-xs font-semibold uppercase tracking-widest text-secondary mb-3">Transparent Pricing</p>
                <h2 id="pricing-heading" class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight mb-4">
                    Plans Built Around Proven Results
                </h2>
                <p class="text-gray-500 leading-relaxed">
                    No hidden fees. No surprises. Every plan includes a written service report and a free re-treatment if pests return.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto items-start">

                <!-- Basic -->
                <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm hover:shadow-md transition-shadow">
                    <p class="text-xs font-extrabold uppercase tracking-widest text-gray-400 mb-2">Basic</p>
                    <h3 class="text-2xl font-extrabold text-gray-900 mb-1">Single Treatment</h3>
                    <p class="text-gray-500 text-sm mb-6 leading-relaxed">Targeted relief for minor or isolated infestations.</p>
                    <ul class="space-y-3 mb-8 text-sm">
                        <?php foreach (['Targeted single treatment','Full inspection report','Written service report'] as $f): ?>
                        <li class="flex items-center gap-2.5 text-gray-600">
                            <i class="fa-solid fa-check text-secondary text-xs flex-shrink-0" aria-hidden="true"></i><?= $f ?>
                        </li>
                        <?php endforeach; ?>
                        <li class="flex items-center gap-2.5 text-gray-900 font-bold">
                            <i class="fa-solid fa-shield-halved text-secondary text-xs flex-shrink-0" aria-hidden="true"></i>30-Day Protection
                        </li>
                    </ul>
                    <a href="<?= $base_url ?>/booking"
                       class="block text-center border-2 border-gray-200 hover:border-primary text-gray-700 hover:text-primary text-sm font-bold py-3 rounded-xl transition">
                        Get Started
                    </a>
                </div>

                <!-- Standard — Featured -->
                <div class="bg-primary rounded-2xl p-8 shadow-2xl relative md:-mt-4 md:mb-4">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                        <span class="bg-secondary text-white text-xs font-extrabold px-5 py-1.5 rounded-full shadow-lg">Most Popular</span>
                    </div>
                    <p class="text-xs font-extrabold uppercase tracking-widest text-secondary mb-2">Standard</p>
                    <h3 class="text-2xl font-extrabold text-white mb-1">Full Treatment</h3>
                    <p class="text-green-100 text-sm mb-6 leading-relaxed">Comprehensive eradication with a dedicated follow-up visit.</p>
                    <ul class="space-y-3 mb-8 text-sm">
                        <?php foreach (['Initial full treatment','1 dedicated follow-up visit','Minor entry point sealing','Detailed inspection report'] as $f): ?>
                        <li class="flex items-center gap-2.5 text-green-100">
                            <i class="fa-solid fa-check text-secondary text-xs flex-shrink-0" aria-hidden="true"></i><?= $f ?>
                        </li>
                        <?php endforeach; ?>
                        <li class="flex items-center gap-2.5 text-white font-bold">
                            <i class="fa-solid fa-shield-halved text-secondary text-xs flex-shrink-0" aria-hidden="true"></i>90-Day Protection
                        </li>
                    </ul>
                    <a href="<?= $base_url ?>/booking"
                       class="block text-center bg-accent hover:bg-yellow-800 text-white text-sm font-extrabold py-3.5 rounded-xl transition shadow-lg">
                        Book Standard Plan
                    </a>
                </div>

                <!-- Premium -->
                <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm hover:shadow-md transition-shadow">
                    <p class="text-xs font-extrabold uppercase tracking-widest text-gray-400 mb-2">Premium</p>
                    <h3 class="text-2xl font-extrabold text-gray-900 mb-1">Full Protection</h3>
                    <p class="text-gray-500 text-sm mb-6 leading-relaxed">Year-round peace of mind for homes and businesses.</p>
                    <ul class="space-y-3 mb-8 text-sm">
                        <?php foreach (['Full treatment + multiple follow-ups','Advanced exclusion & proofing','Seasonal preventive visits','Priority booking & dispatch'] as $f): ?>
                        <li class="flex items-center gap-2.5 text-gray-600">
                            <i class="fa-solid fa-check text-secondary text-xs flex-shrink-0" aria-hidden="true"></i><?= $f ?>
                        </li>
                        <?php endforeach; ?>
                        <li class="flex items-center gap-2.5 text-gray-900 font-bold">
                            <i class="fa-solid fa-shield-halved text-secondary text-xs flex-shrink-0" aria-hidden="true"></i>6–12 Month Protection
                        </li>
                    </ul>
                    <a href="<?= $base_url ?>/booking"
                       class="block text-center border-2 border-gray-200 hover:border-primary text-gray-700 hover:text-primary text-sm font-bold py-3 rounded-xl transition">
                        Get Full Protection
                    </a>
                </div>

            </div>

            <p class="text-center text-xs text-gray-400 mt-8">
                <i class="fa-solid fa-circle-info mr-1" aria-hidden="true"></i>
                All plans include a free on-site assessment. Exact pricing depends on property size and severity of infestation.
                <a href="<?= $base_url ?>/contact" class="text-primary hover:underline ml-1">Contact us for a custom commercial quote.</a>
            </p>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         FINAL CTA — Urgency banner
    ══════════════════════════════════════════════════════════ -->
    <section class="bg-accent py-14">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight mb-3">
                Ready to Be Pest-Free?
            </h2>
            <p class="text-green-100 mb-7 max-w-lg mx-auto">
                Get a free, no-obligation inspection and quote today. Our team is standing by.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="<?= $base_url ?>/booking"
                   class="inline-flex items-center gap-2 bg-white text-accent hover:bg-yellow-50 font-extrabold px-8 py-4 rounded-xl transition shadow-xl text-sm">
                    <i class="fa-solid fa-calendar-check" aria-hidden="true"></i>
                    Book Free Inspection
                </a>
                <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                   class="inline-flex items-center gap-2 bg-dark/30 hover:bg-dark/50 border border-white/20 text-white font-bold px-8 py-4 rounded-xl transition text-sm">
                    <i class="fa-solid fa-phone" aria-hidden="true"></i>
                    <?= htmlspecialchars($site_phone) ?>
                </a>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         INTERNAL LINKS
    ══════════════════════════════════════════════════════════ -->
    <section class="py-14 bg-white border-t border-gray-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <a href="<?= $base_url ?>/faq"
                   class="flex items-start gap-4 bg-gray-50 rounded-2xl p-6 hover:bg-gray-100 transition group">
                    <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-circle-question text-primary" aria-hidden="true"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 mb-1 group-hover:text-primary transition">Frequently Asked Questions</p>
                        <p class="text-sm text-gray-500">How long do treatments take? Are they safe for my pets? Find all the answers.</p>
                    </div>
                </a>
                <a href="<?= $base_url ?>/blogs"
                   class="flex items-start gap-4 bg-gray-50 rounded-2xl p-6 hover:bg-gray-100 transition group">
                    <div class="w-11 h-11 rounded-xl bg-secondary/10 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-newspaper text-secondary" aria-hidden="true"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 mb-1 group-hover:text-primary transition">Pest Control Tips & Guides</p>
                        <p class="text-sm text-gray-500">Expert articles on identifying and preventing pest problems in Sydney &amp; Brisbane.</p>
                    </div>
                </a>
            </div>
        </div>
    </section>

</main>

<?php require_once BASE_DIR . '/includes/footer.php'; ?>
