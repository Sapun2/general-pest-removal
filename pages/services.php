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

// Load services from DB with hardcoded fallback
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
        ['id'=>1,'slug'=>'termites',    'name'=>'Termite Inspection & Treatment','tagline'=>'Thermal imaging inspections. Proven barrier treatments.','description'=>'<p>Sydney and Brisbane have some of the highest termite activity rates in Australia. Our licensed inspectors use thermal imaging cameras and moisture meters to detect activity without drilling or destructive testing.</p>','icon'=>'fa-house-crack','badge_text'=>'Most Critical Service','image_path'=>'/assets/images/2.png','sort_order'=>1,'is_active'=>1,'features'=>[['icon'=>'fa-camera','title'=>'Thermal Imaging Inspection','desc'=>'Non-invasive detection using FLIR thermal cameras.'],['icon'=>'fa-shield-halved','title'=>'Reticulation Barrier Systems','desc'=>'Continuous chemical barrier systems protecting your full perimeter.'],['icon'=>'fa-file-contract','title'=>'AS 3660.2-Compliant Reports','desc'=>'Full written inspection reports required by lenders and conveyancers.']]],
        ['id'=>2,'slug'=>'cockroaches', 'name'=>'Cockroach Control','tagline'=>'Colony-level elimination — not just the ones you see.','description'=>'<p>Our IPM-based approach targets harborage zones with pharmaceutical-grade gel baits that the entire colony ingests, eliminating the source in a single treatment cycle.</p>','icon'=>'fa-bug','badge_text'=>'#1 Australian Pest Problem','image_path'=>'/assets/images/3.png','sort_order'=>2,'is_active'=>1,'features'=>[['icon'=>'fa-droplet','title'=>'Gel Bait Treatments','desc'=>'Slow-acting colony-level baits applied to harborage zones.'],['icon'=>'fa-spray-can','title'=>'Residual Surface Sprays','desc'=>'Long-lasting residual products applied to entry points.'],['icon'=>'fa-leaf','title'=>'Pet & Family Safe','desc'=>'Low-toxicity APVMA-registered formulations.']]],
        ['id'=>3,'slug'=>'spiders',     'name'=>'Spider Removal','tagline'=>'Redbacks, Funnel-webs & White-tails — safely eliminated.','description'=>'<p>We provide comprehensive spider removal and preventive webbing spray treatments that create a long-lasting barrier around your home\'s exterior, eaves, and entry points.</p>','icon'=>'fa-spider','badge_text'=>'Family Safe Methods','image_path'=>'/assets/images/4.png','sort_order'=>3,'is_active'=>1,'features'=>[['icon'=>'fa-house','title'=>'Exterior Webbing Treatment','desc'=>'Full perimeter spray targeting eaves and entry points.'],['icon'=>'fa-magnifying-glass','title'=>'Harborage Identification','desc'=>'Locate and treat common hiding areas.'],['icon'=>'fa-child-reaching','title'=>'Child & Pet Safe Options','desc'=>'Synthetic pyrethroid formulas with short re-entry times.']]],
        ['id'=>4,'slug'=>'ants',        'name'=>'Ant Treatment','tagline'=>'Fire ants, coastal brown ants & more — source eliminated.','description'=>'<p>We identify the species accurately before treatment — because the wrong product on the wrong ant makes infestations worse.</p>','icon'=>'fa-circle-dot','badge_text'=>'High Priority Pest','image_path'=>'/assets/images/2.png','sort_order'=>4,'is_active'=>1,'features'=>[['icon'=>'fa-magnifying-glass','title'=>'Species Identification','desc'=>'Correct species ID before treatment.'],['icon'=>'fa-circle-dot','title'=>'Granular & Liquid Baits','desc'=>'Slow-transfer baits eliminating the queen and reproductives.'],['icon'=>'fa-border-all','title'=>'Perimeter Barrier','desc'=>'Residual perimeter treatment preventing re-entry.']]],
        ['id'=>5,'slug'=>'rodents',     'name'=>'Rodent Control','tagline'=>'Rats & mice — eliminated and excluded permanently.','description'=>'<p>We don\'t just bait — we conduct a full entry-point audit and seal every gap, crack, and pipe penetration with rodent-proof materials so they cannot return.</p>','icon'=>'fa-cheese','badge_text'=>'Year-Round Problem','image_path'=>'/assets/images/3.png','sort_order'=>5,'is_active'=>1,'features'=>[['icon'=>'fa-hammer','title'=>'Full Exclusion Sealing','desc'=>'Steel wool, wire mesh, and expanding foam applied to all entry points.'],['icon'=>'fa-cheese','title'=>'Tamper-Resistant Baiting','desc'=>'Secure bait stations in key pathways.'],['icon'=>'fa-calendar','title'=>'Ongoing Monitoring Plans','desc'=>'Regular inspections for commercial and high-risk properties.']]],
    ];
}

$page_seo = get_page_seo('services', [
    'title'       => 'Pest Removal Services in Sydney & Brisbane | Termites, Cockroaches, Spiders',
    'description' => 'Professional termite inspections, cockroach control, spider removal, ant treatment and rodent control across Sydney & Brisbane. Licensed & insured. Book online today.',
    'canonical'   => SITE_BASE_URL . '/services',
    'breadcrumbs' => [
        ['name' => 'Home',     'url' => '/'],
        ['name' => 'Services', 'url' => '/services'],
    ],
]);

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
                    <li aria-current="page" class="text-slate-300">Services</li>
                </ol>
            </nav>

            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 bg-green-600/15 border border-green-500/20 rounded-full px-4 py-1.5 mb-6">
                        <i class="fa-solid fa-certificate text-green-400 text-xs" aria-hidden="true"></i>
                        <span class="text-green-400 text-xs font-semibold uppercase tracking-wide">Licensed · Fully Insured</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight mb-5 leading-tight">
                        Pest Removal Services<br>
                        <span class="text-green-400">Across Sydney &amp; Brisbane</span>
                    </h1>
                    <p class="text-slate-300 leading-relaxed mb-8 max-w-lg text-lg">
                        Science-based, eco-friendly pest elimination for residential and commercial properties. We respond within 2 hours.
                    </p>

                    <div class="flex flex-wrap gap-2 mb-8">
                        <?php
                        $badges = ['2-Hour Response', 'Free Retreatment', 'Pet & Child Safe', '4.9★ Rated'];
                        foreach ($badges as $badge): ?>
                        <span class="inline-flex items-center gap-1.5 bg-white/8 border border-white/12 text-slate-300 text-xs font-medium px-3 py-1.5 rounded-full">
                            <i class="fa-solid fa-check text-green-400 text-[10px]" aria-hidden="true"></i>
                            <?= $badge ?>
                        </span>
                        <?php endforeach; ?>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="<?= $base_url ?>/booking"
                           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition text-sm">
                            <i class="fa-solid fa-calendar-check text-xs" aria-hidden="true"></i>
                            Book Free Inspection
                        </a>
                        <a href="#pricing"
                           class="inline-flex items-center gap-2 border border-white/15 hover:border-white/25 text-white font-medium px-6 py-3 rounded-lg transition text-sm">
                            View Pricing
                        </a>
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

    <!-- ── SERVICE CARDS ─────────────────────────────────────────── -->
    <section class="py-16 md:py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-xl mx-auto mb-12">
                <p class="text-xs font-semibold uppercase tracking-widest text-green-600 mb-3">What We Treat</p>
                <h2 class="text-2xl md:text-3xl font-bold text-slate-900 tracking-tight">
                    All Services — Book Any Online
                </h2>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php
                $guarantee_map = [0 => '30-Day', 1 => '90-Day', 2 => '6-Month', 3 => '90-Day', 4 => '6-Month'];
                foreach ($services_data as $idx => $svc):
                    $slug      = htmlspecialchars($svc['slug']);
                    $icon      = htmlspecialchars($svc['icon'] ?? 'fa-bug');
                    $guarantee = ($guarantee_map[$idx] ?? '30-Day') . ' Protection';
                    $features  = array_slice($svc['features'] ?? [], 0, 3);
                ?>
                <div class="bg-white rounded-xl border border-slate-200 flex flex-col overflow-hidden hover:shadow-md transition-shadow">
                    <?php if (!empty($svc['image_path'])): ?>
                    <div class="relative h-44 overflow-hidden flex-shrink-0">
                        <img src="<?= $base_url . htmlspecialchars($svc['image_path']) ?>"
                             alt="<?= htmlspecialchars($svc['name']) ?>"
                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                             loading="lazy">
                        <?php if (!empty($svc['badge_text'])): ?>
                        <div class="absolute top-3 left-3">
                            <span class="text-xs font-semibold text-white bg-slate-900/70 backdrop-blur-sm px-2.5 py-1 rounded-full">
                                <?= htmlspecialchars($svc['badge_text']) ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <div class="h-20 bg-green-50 border-b border-slate-200 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid <?= $icon ?> text-green-600 text-3xl" aria-hidden="true"></i>
                    </div>
                    <?php endif; ?>

                    <div class="px-5 pt-4 pb-3">
                        <h3 class="font-bold text-slate-900 text-[15px] mb-1"><?= htmlspecialchars($svc['name']) ?></h3>
                        <?php if (!empty($svc['tagline'])): ?>
                        <p class="text-xs text-slate-500 leading-snug"><?= htmlspecialchars($svc['tagline']) ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($features)): ?>
                    <ul class="px-5 pb-4 space-y-1.5 flex-grow">
                        <?php foreach ($features as $feat): ?>
                        <li class="flex items-start gap-2 text-xs text-slate-600">
                            <i class="fa-solid fa-check text-green-600 text-[10px] flex-shrink-0 mt-0.5" aria-hidden="true"></i>
                            <span><?= htmlspecialchars($feat['title']) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>

                    <div class="px-5 pb-5 pt-3 border-t border-slate-100 mt-auto flex items-center justify-between gap-3">
                        <span class="text-xs text-green-700 flex items-center gap-1.5 font-medium">
                            <i class="fa-solid fa-shield-halved text-[10px]" aria-hidden="true"></i>
                            <?= $guarantee ?>
                        </span>
                        <a href="<?= $base_url ?>/booking?service=<?= $slug ?>"
                           class="inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-4 py-2.5 rounded-lg transition">
                            <i class="fa-regular fa-calendar-check text-[10px]" aria-hidden="true"></i>
                            Book Now
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Not sure card -->
                <div class="bg-dark rounded-xl border border-white/8 flex flex-col items-center justify-center text-center p-8 gap-4">
                    <div class="w-12 h-12 bg-green-600/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-circle-question text-green-400 text-xl" aria-hidden="true"></i>
                    </div>
                    <div>
                        <p class="font-bold text-white text-base mb-1">Not sure?</p>
                        <p class="text-slate-400 text-xs leading-relaxed">Tell us what you're seeing — we'll identify and treat it.</p>
                    </div>
                    <a href="<?= $base_url ?>/booking"
                       class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-6 py-3 rounded-lg transition w-full justify-center">
                        <i class="fa-regular fa-calendar-check" aria-hidden="true"></i>
                        Get Free Quote
                    </a>
                    <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                       class="text-xs text-slate-400 hover:text-white transition flex items-center gap-1.5">
                        <i class="fa-solid fa-phone text-[10px]" aria-hidden="true"></i>
                        <?= htmlspecialchars($site_phone) ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ── STATS STRIP ───────────────────────────────────────────── -->
    <section class="bg-dark py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <?php
                $stats = [
                    ['500+',  'Jobs Completed',     'fa-briefcase'],
                    ['4.9★',  'Google Rating',      'fa-star'],
                    ['2 hrs', 'Avg. Response Time', 'fa-bolt'],
                    ['100%',  'Satisfaction Assured','fa-shield-halved'],
                ];
                foreach ($stats as [$num, $label, $icon]): ?>
                <div class="py-4">
                    <i class="fa-solid <?= $icon ?> text-green-400 text-xl mb-3 block" aria-hidden="true"></i>
                    <p class="text-3xl font-bold text-white mb-1"><?= $num ?></p>
                    <p class="text-slate-400 text-sm"><?= $label ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ── PRICING ───────────────────────────────────────────────── -->
    <section id="pricing" class="py-20 md:py-28 bg-white scroll-mt-20" aria-labelledby="pricing-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <p class="text-xs font-semibold uppercase tracking-widest text-green-600 mb-3">Transparent Pricing</p>
                <h2 id="pricing-heading" class="text-3xl md:text-4xl font-bold text-slate-900 tracking-tight mb-4">
                    Plans Built Around Proven Results
                </h2>
                <p class="text-slate-500 leading-relaxed text-sm">
                    No hidden fees. No surprises. Every plan includes a written service report and free retreatment if pests return.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto items-start">

                <!-- Basic -->
                <div class="bg-white rounded-xl border border-slate-200 p-8 hover:shadow-md transition-shadow">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Basic</p>
                    <h3 class="text-2xl font-bold text-slate-900 mb-1">Single Treatment</h3>
                    <p class="text-slate-500 text-sm mb-6 leading-relaxed">Targeted relief for minor or isolated infestations.</p>
                    <ul class="space-y-3 mb-8 text-sm">
                        <?php foreach (['Targeted single treatment','Full inspection report','Written service report'] as $f): ?>
                        <li class="flex items-center gap-2.5 text-slate-600">
                            <i class="fa-solid fa-check text-green-600 text-xs flex-shrink-0" aria-hidden="true"></i>
                            <?= $f ?>
                        </li>
                        <?php endforeach; ?>
                        <li class="flex items-center gap-2.5 text-slate-900 font-semibold">
                            <i class="fa-solid fa-shield-halved text-green-600 text-xs flex-shrink-0" aria-hidden="true"></i>
                            30-Day Protection
                        </li>
                    </ul>
                    <a href="<?= $base_url ?>/booking"
                       class="block text-center border border-slate-200 hover:border-green-500 text-slate-700 hover:text-green-700 text-sm font-semibold py-3 rounded-lg transition">
                        Get Started
                    </a>
                </div>

                <!-- Standard — Featured -->
                <div class="bg-dark rounded-xl p-8 shadow-2xl relative md:-mt-4 md:mb-4 border border-white/10">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                        <span class="bg-green-600 text-white text-xs font-semibold px-5 py-1.5 rounded-full shadow-lg">Most Popular</span>
                    </div>
                    <p class="text-xs font-bold uppercase tracking-widest text-green-400 mb-2">Standard</p>
                    <h3 class="text-2xl font-bold text-white mb-1">Full Treatment</h3>
                    <p class="text-slate-400 text-sm mb-6 leading-relaxed">Comprehensive eradication with a dedicated follow-up visit.</p>
                    <ul class="space-y-3 mb-8 text-sm">
                        <?php foreach (['Initial full treatment','1 dedicated follow-up visit','Minor entry point sealing','Detailed inspection report'] as $f): ?>
                        <li class="flex items-center gap-2.5 text-slate-300">
                            <i class="fa-solid fa-check text-green-400 text-xs flex-shrink-0" aria-hidden="true"></i>
                            <?= $f ?>
                        </li>
                        <?php endforeach; ?>
                        <li class="flex items-center gap-2.5 text-white font-semibold">
                            <i class="fa-solid fa-shield-halved text-green-400 text-xs flex-shrink-0" aria-hidden="true"></i>
                            90-Day Protection
                        </li>
                    </ul>
                    <a href="<?= $base_url ?>/booking"
                       class="block text-center bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-3.5 rounded-lg transition">
                        Book Standard Plan
                    </a>
                </div>

                <!-- Premium -->
                <div class="bg-white rounded-xl border border-slate-200 p-8 hover:shadow-md transition-shadow">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Premium</p>
                    <h3 class="text-2xl font-bold text-slate-900 mb-1">Full Protection</h3>
                    <p class="text-slate-500 text-sm mb-6 leading-relaxed">Year-round peace of mind for homes and businesses.</p>
                    <ul class="space-y-3 mb-8 text-sm">
                        <?php foreach (['Full treatment + multiple follow-ups','Advanced exclusion & proofing','Seasonal preventive visits','Priority booking & dispatch'] as $f): ?>
                        <li class="flex items-center gap-2.5 text-slate-600">
                            <i class="fa-solid fa-check text-green-600 text-xs flex-shrink-0" aria-hidden="true"></i>
                            <?= $f ?>
                        </li>
                        <?php endforeach; ?>
                        <li class="flex items-center gap-2.5 text-slate-900 font-semibold">
                            <i class="fa-solid fa-shield-halved text-green-600 text-xs flex-shrink-0" aria-hidden="true"></i>
                            6–12 Month Protection
                        </li>
                    </ul>
                    <a href="<?= $base_url ?>/booking"
                       class="block text-center border border-slate-200 hover:border-green-500 text-slate-700 hover:text-green-700 text-sm font-semibold py-3 rounded-lg transition">
                        Get Full Protection
                    </a>
                </div>

            </div>

            <p class="text-center text-xs text-slate-400 mt-8">
                All plans include a free on-site assessment. Pricing depends on property size and infestation severity.
                <a href="<?= $base_url ?>/contact" class="text-green-600 hover:underline ml-1">Contact us for a custom commercial quote.</a>
            </p>
        </div>
    </section>

    <!-- ── CTA ───────────────────────────────────────────────────── -->
    <section class="bg-dark border-t border-white/5 py-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl md:text-3xl font-bold text-white tracking-tight mb-3">
                Ready to Be Pest-Free?
            </h2>
            <p class="text-slate-300 mb-8 max-w-lg mx-auto">
                Get a free, no-obligation inspection and quote today. Our team is standing by.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="<?= $base_url ?>/booking"
                   class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-4 rounded-lg transition text-sm">
                    <i class="fa-solid fa-calendar-check" aria-hidden="true"></i>
                    Book Free Inspection
                </a>
                <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                   class="inline-flex items-center gap-2 border border-white/15 hover:border-white/25 text-white font-medium px-8 py-4 rounded-lg transition text-sm">
                    <i class="fa-solid fa-phone text-xs text-green-400" aria-hidden="true"></i>
                    <?= htmlspecialchars($site_phone) ?>
                </a>
            </div>
        </div>
    </section>

    <!-- ── INTERNAL LINKS ────────────────────────────────────────── -->
    <section class="py-12 bg-white border-t border-slate-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid sm:grid-cols-2 gap-4">
                <a href="<?= $base_url ?>/faq"
                   class="flex items-start gap-4 bg-slate-50 rounded-xl p-5 hover:bg-slate-100 transition group">
                    <div class="w-10 h-10 rounded-lg bg-green-50 border border-green-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-circle-question text-green-600 text-sm" aria-hidden="true"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900 mb-1 text-sm group-hover:text-green-700 transition">Frequently Asked Questions</p>
                        <p class="text-xs text-slate-500">Treatment duration, pet safety, what to expect.</p>
                    </div>
                </a>
                <a href="<?= $base_url ?>/blogs"
                   class="flex items-start gap-4 bg-slate-50 rounded-xl p-5 hover:bg-slate-100 transition group">
                    <div class="w-10 h-10 rounded-lg bg-green-50 border border-green-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-newspaper text-green-600 text-sm" aria-hidden="true"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900 mb-1 text-sm group-hover:text-green-700 transition">Pest Control Tips &amp; Guides</p>
                        <p class="text-xs text-slate-500">Expert articles on identifying and preventing pest problems.</p>
                    </div>
                </a>
            </div>
        </div>
    </section>

</main>

<?php require_once BASE_DIR . '/includes/footer.php'; ?>
