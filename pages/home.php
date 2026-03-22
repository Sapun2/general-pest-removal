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

$selected_service = htmlspecialchars(trim($_GET['service'] ?? $form_data['pest_type'] ?? ''), ENT_QUOTES, 'UTF-8');

$page_seo = get_page_seo('home', [
    'title'          => 'Top Rated Pest Control in Sydney & Brisbane | General Pest Removal',
    'description'    => 'Expert pest control in Sydney, Brisbane, Parramatta, Inner West, North Shore & surrounds. Termite inspections, rodent exclusion, cockroach control. Licensed & insured. Book online today.',
    'canonical'      => SITE_BASE_URL . '/',
    'og_title'       => 'General Pest Removal — Fast, Proven Pest Removal in Sydney & Brisbane',
    'og_description' => 'Australia\'s trusted pest control company. We eliminate termites, cockroaches, spiders, and rodents across Sydney & Brisbane with up to 12 months of protection.',
    'schema'         => [
        '@context' => 'https://schema.org',
        '@graph'   => [[
            '@type'           => 'LocalBusiness',
            '@id'             => SITE_BASE_URL . '/#business',
            'name'            => 'General Pest Removal',
            'url'             => SITE_BASE_URL . '/',
            'telephone'       => SITE_PHONE_RAW,
            'email'           => SITE_EMAIL,
            'priceRange'      => '$$',
            'address'         => ['@type' => 'PostalAddress', 'addressLocality' => 'Sydney', 'addressRegion' => 'NSW', 'addressCountry' => 'AU'],
            'aggregateRating' => ['@type' => 'AggregateRating', 'ratingValue' => '4.9', 'reviewCount' => '200'],
        ]],
    ],
]);

require_once BASE_DIR . '/includes/header.php';
?>

<main class="flex-grow">

<!-- ── HERO ──────────────────────────────────────────────────────── -->
<section class="bg-dark" aria-label="Hero">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20">
        <div class="grid lg:grid-cols-[1fr_420px] xl:grid-cols-[1fr_460px] gap-10 lg:gap-16 items-start">

            <!-- Left: headline + trust -->
            <div class="order-2 lg:order-1 lg:pt-4">

                <!-- Rating badge -->
                <div class="inline-flex items-center gap-2 mb-6">
                    <div class="flex items-center gap-0.5">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118L10 14.347l-3.37 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.644 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69L9.049 2.927z"/>
                        </svg>
                        <?php endfor; ?>
                    </div>
                    <span class="text-white font-semibold text-sm">4.9</span>
                    <span class="text-slate-400 text-sm">· 200+ verified reviews</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-[52px] font-bold text-white leading-[1.1] tracking-tight mb-5">
                    Australia's Trusted<br>
                    Pest Removal<br>
                    <span class="text-green-400">Specialists.</span>
                </h1>

                <p class="text-slate-300 text-lg leading-relaxed mb-8 max-w-md">
                    Cockroaches, termites, spiders, rodents — eliminated fast. Licensed technicians, same-day response across Sydney &amp; Brisbane.
                </p>

                <!-- Trust bullets -->
                <ul class="space-y-3 mb-8" role="list">
                    <?php
                    $bullets = [
                        ['fa-bolt-lightning',  'Same-day &amp; emergency appointments available'],
                        ['fa-shield-halved',   'Licensed &amp; fully insured technicians'],
                        ['fa-rotate-left',     'Free retreatment if pests return — guaranteed'],
                        ['fa-car',             'Discreet, unmarked vehicles — complete privacy'],
                    ];
                    foreach ($bullets as [$icon, $text]): ?>
                    <li class="flex items-center gap-3 text-slate-300 text-sm">
                        <span class="w-6 h-6 rounded-md bg-green-600/20 border border-green-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid <?= $icon ?> text-green-400 text-[10px]" aria-hidden="true"></i>
                        </span>
                        <?= $text ?>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Phone CTA -->
                <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                   class="inline-flex items-center gap-3 border border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/8 text-white font-semibold px-5 py-3 rounded-xl transition text-sm">
                    <span class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-phone text-white text-xs" aria-hidden="true"></i>
                    </span>
                    <span>
                        <span class="block text-[10px] text-slate-400 font-normal leading-none mb-0.5">24/7 Emergency Line</span>
                        <?= htmlspecialchars($site_phone) ?>
                    </span>
                </a>

                <!-- Stats row — desktop only -->
                <div class="hidden lg:flex items-center gap-8 mt-10 pt-8 border-t border-white/8">
                    <?php foreach (['500+' => 'Jobs completed', '12mo' => 'Max protection', '2hr' => 'Avg. callback', '100%' => 'Satisfaction rate'] as $num => $label): ?>
                    <div>
                        <p class="text-2xl font-bold text-white leading-none"><?= $num ?></p>
                        <p class="text-xs text-slate-400 mt-0.5"><?= $label ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right: Booking form -->
            <div class="order-1 lg:order-2 w-full" id="booking-form-anchor">
                <div class="bg-white rounded-2xl overflow-hidden shadow-2xl border-t-4 border-green-600">

                    <!-- Card heading -->
                    <div class="px-6 pt-6 pb-4 border-b border-slate-100">
                        <h2 class="text-lg font-bold text-slate-900 leading-tight">Book a Free Inspection</h2>
                        <p class="text-sm text-slate-500 mt-1 flex items-center gap-1.5">
                            <i class="fa-regular fa-clock text-green-600 text-xs" aria-hidden="true"></i>
                            We call back within 2 hours during business hours.
                        </p>
                    </div>

                    <div class="px-6 py-5">
                        <?php if ($flash_error): ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 text-xs px-3 py-2.5 rounded-lg mb-4 flex items-start gap-2">
                            <i class="fa-solid fa-triangle-exclamation flex-shrink-0 mt-0.5" aria-hidden="true"></i>
                            <?= htmlspecialchars($flash_error) ?>
                        </div>
                        <?php endif; ?>

                        <form action="<?= $base_url ?>/process_booking" method="POST" class="space-y-4" novalidate id="hero-form">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                            <!-- Name row -->
                            <div class="grid grid-cols-2 gap-3">
                                <?php foreach ([['h_first_name','first_name','given-name','First Name','Jane'], ['h_last_name','last_name','family-name','Last Name','Smith']] as [$id,$name,$ac,$label,$ph]): ?>
                                <div>
                                    <label for="<?= $id ?>" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        <?= $label ?> <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="<?= $id ?>" name="<?= $name ?>" required
                                           autocomplete="<?= $ac ?>" placeholder="<?= $ph ?>"
                                           value="<?= htmlspecialchars($form_data[$name] ?? '') ?>"
                                           class="w-full px-3.5 py-3 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/10 text-sm text-slate-900 placeholder:text-slate-400 transition">
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="h_phone" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" id="h_phone" name="phone" required
                                       autocomplete="tel" placeholder="(02) 8155 0198"
                                       value="<?= htmlspecialchars($form_data['phone'] ?? '') ?>"
                                       class="w-full px-3.5 py-3 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/10 text-sm text-slate-900 placeholder:text-slate-400 transition">
                            </div>

                            <!-- Pest Type -->
                            <div>
                                <label for="h_pest_type" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    What Pest? <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select id="h_pest_type" name="pest_type" required
                                            class="w-full appearance-none px-3.5 py-3 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/10 text-sm text-slate-900 transition pr-10">
                                        <option value="">Select pest type...</option>
                                        <option value="bedbugs"     <?= $selected_service === 'bedbugs'     ? 'selected' : '' ?>>Bed Bugs</option>
                                        <option value="rodents"     <?= $selected_service === 'rodents'     ? 'selected' : '' ?>>Mice &amp; Rats</option>
                                        <option value="cockroaches" <?= $selected_service === 'cockroaches' ? 'selected' : '' ?>>Cockroaches</option>
                                        <option value="ants"        <?= $selected_service === 'ants'        ? 'selected' : '' ?>>Ants</option>
                                        <option value="wasps"       <?= $selected_service === 'wasps'       ? 'selected' : '' ?>>Wasps &amp; Hornets</option>
                                        <option value="wildlife"    <?= $selected_service === 'wildlife'    ? 'selected' : '' ?>>Wildlife</option>
                                        <option value="other">Not Sure / Other</option>
                                    </select>
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                        <i class="fa-solid fa-chevron-down text-xs" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Street Address -->
                            <div>
                                <label for="h_street_address" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Street Address <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="h_street_address" name="street_address" required
                                       autocomplete="street-address"
                                       placeholder="123 Main St, Sydney NSW"
                                       value="<?= htmlspecialchars($form_data['street_address'] ?? '') ?>"
                                       class="w-full px-3.5 py-3 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/10 text-sm text-slate-900 placeholder:text-slate-400 transition">
                            </div>

                            <!-- Submit -->
                            <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold text-sm py-3.5 rounded-lg transition flex items-center justify-center gap-2">
                                <i class="fa-regular fa-calendar-check" aria-hidden="true"></i>
                                Request Free Inspection
                            </button>
                        </form>

                        <p class="text-center text-xs text-slate-400 mt-3 flex items-center justify-center gap-3">
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-lock text-slate-300 text-[10px]" aria-hidden="true"></i>
                                Secure &amp; private
                            </span>
                            <span class="w-px h-3 bg-slate-200"></span>
                            <span>No credit card</span>
                            <span class="w-px h-3 bg-slate-200"></span>
                            <span>100% free</span>
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ── TRUST STRIP ───────────────────────────────────────────────── -->
<section class="bg-white border-b border-slate-100" aria-label="Trust signals">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 divide-x divide-y lg:divide-y-0 divide-slate-100">
            <?php
            $trust = [
                ['fa-star',        'text-yellow-500', '4.9 / 5',    '200+ verified Google reviews'],
                ['fa-bolt',        'text-green-600',  '2 Hours',    'Average callback time'],
                ['fa-rotate-left', 'text-green-600',  'Free',       'Retreatment if pests return'],
                ['fa-id-badge',    'text-green-600',  'Licensed',   'Fully insured technicians'],
            ];
            foreach ($trust as [$icon, $color, $num, $label]): ?>
            <div class="flex items-center gap-3.5 px-5 py-5">
                <div class="w-10 h-10 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid <?= $icon ?> <?= $color ?>" aria-hidden="true"></i>
                </div>
                <div>
                    <p class="font-bold text-slate-900 text-sm leading-tight"><?= $num ?></p>
                    <p class="text-xs text-slate-500 mt-0.5 leading-snug"><?= $label ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ── SERVICES ──────────────────────────────────────────────────── -->
<section class="py-20 lg:py-28 bg-slate-50" aria-labelledby="services-heading">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center max-w-xl mx-auto mb-14">
            <p class="text-xs font-semibold uppercase tracking-widest text-green-600 mb-3">What We Treat</p>
            <h2 id="services-heading" class="text-3xl lg:text-4xl font-bold text-slate-900 tracking-tight mb-4">
                Specialized Solutions for Every Pest
            </h2>
            <p class="text-slate-500 text-sm leading-relaxed">
                Science-based, environmentally responsible methods — complete eradication backed by a written service report.
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            $services = [
                [
                    'img'       => '/assets/images/2.png',
                    'alt'       => 'Termite inspection Sydney & Brisbane',
                    'badge'     => 'Most Requested',
                    'title'     => 'Termite Inspection &amp; Treatment',
                    'desc'      => 'AS 3660.2-compliant thermal imaging inspections and chemical barrier treatments — complete termite eradication with written service report.',
                    'features'  => ['AS 3660.2 Australian Standard compliant', 'Same-day emergency service', 'Written treatment report on every job'],
                    'guarantee' => '12-Month Protection',
                    'book_href' => '/booking?service=termites',
                    'info_href' => '/services#termites',
                ],
                [
                    'img'       => '/assets/images/3.png',
                    'alt'       => 'Cockroach control Sydney & Brisbane',
                    'badge'     => 'Year-Round',
                    'title'     => 'Cockroach Control',
                    'desc'      => 'IPM-based gel baiting eliminates the entire colony at the source — safe for kitchens, restaurants, and family homes.',
                    'features'  => ['Targets eggs and the colony queen', 'Safe for food-preparation environments', 'Commercial contracts available'],
                    'guarantee' => '6-Month Protection',
                    'book_href' => '/booking?service=cockroaches',
                    'info_href' => '/services#cockroaches',
                ],
                [
                    'img'       => '/assets/images/4.png',
                    'alt'       => 'Spider and ant removal Sydney & Brisbane',
                    'badge'     => 'Residential &amp; Commercial',
                    'title'     => 'Spider &amp; Ant Removal',
                    'desc'      => 'Redback, Funnel-web, and common spider removal plus targeted ant treatments — protecting your family year-round.',
                    'features'  => ['Redback &amp; Funnel-web specialists', 'Pet and child-safe treatments', 'Fire Ant biosecurity compliant'],
                    'guarantee' => '3-Month Protection',
                    'book_href' => '/booking?service=spiders',
                    'info_href' => '/services#spiders',
                ],
            ];
            foreach ($services as $s): ?>
            <article class="bg-white rounded-xl border border-slate-200 overflow-hidden flex flex-col hover:shadow-md transition-shadow duration-300">
                <div class="relative aspect-[16/9] overflow-hidden">
                    <img src="<?= $base_url . $s['img'] ?>"
                         alt="<?= $s['alt'] ?>"
                         class="w-full h-full object-cover hover:scale-[1.03] transition-transform duration-500"
                         loading="lazy">
                    <span class="absolute top-3 left-3 bg-white/90 text-slate-700 text-xs font-semibold px-2.5 py-1 rounded-full backdrop-blur-sm">
                        <?= $s['badge'] ?>
                    </span>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-base font-bold text-slate-900 mb-2"><?= $s['title'] ?></h3>
                    <p class="text-slate-500 text-sm leading-relaxed mb-4"><?= $s['desc'] ?></p>

                    <ul class="space-y-1.5 mb-5" role="list">
                        <?php foreach ($s['features'] as $f): ?>
                        <li class="flex items-start gap-2 text-xs text-slate-600">
                            <i class="fa-solid fa-check text-green-600 mt-0.5 flex-shrink-0 text-[10px]" aria-hidden="true"></i>
                            <?= $f ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 bg-green-50 border border-green-100 px-2.5 py-1 rounded-full flex-shrink-0">
                            <i class="fa-solid fa-shield-halved text-[10px]" aria-hidden="true"></i>
                            <?= $s['guarantee'] ?>
                        </span>
                        <div class="flex items-center gap-2">
                            <a href="<?= $base_url . $s['info_href'] ?>"
                               class="text-xs font-medium text-slate-400 hover:text-slate-600 transition px-2 py-1">
                                Details
                            </a>
                            <a href="<?= $base_url . $s['book_href'] ?>"
                               class="inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition">
                                <i class="fa-regular fa-calendar-check text-[10px]" aria-hidden="true"></i>
                                Book
                            </a>
                        </div>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <p class="text-center mt-10">
            <a href="<?= $base_url ?>/services"
               class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-green-700 border border-slate-200 hover:border-green-300 bg-white px-6 py-3 rounded-lg transition">
                View all services &amp; pricing
                <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
            </a>
        </p>
    </div>
</section>


<!-- ── HOW IT WORKS ──────────────────────────────────────────────── -->
<section class="py-20 lg:py-28 bg-white" aria-labelledby="process-heading">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-14">
            <p class="text-xs font-semibold uppercase tracking-widest text-green-600 mb-3">Simple Process</p>
            <h2 id="process-heading" class="text-3xl lg:text-4xl font-bold text-slate-900 tracking-tight">
                Pest-Free in 3 Steps
            </h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
            <?php
            $steps = [
                ['num' => '01', 'icon' => 'fa-clipboard-list',    'title' => 'Fill the Quick Form',  'desc' => 'Takes under 60 seconds. Your name, phone, address, and the pest you\'re dealing with.'],
                ['num' => '02', 'icon' => 'fa-phone-volume',       'title' => 'We Call You Back',     'desc' => 'A licensed technician calls within 2 hours to confirm your appointment and answer questions.'],
                ['num' => '03', 'icon' => 'fa-house-circle-check', 'title' => 'Problem Solved',       'desc' => 'We treat your property and provide a written service report. Pests return? We retreat free.'],
            ];
            foreach ($steps as $step): ?>
            <div class="text-center">
                <div class="w-14 h-14 bg-green-600 rounded-xl flex items-center justify-center mx-auto mb-5">
                    <i class="fa-solid <?= $step['icon'] ?> text-white text-xl" aria-hidden="true"></i>
                </div>
                <p class="text-xs font-bold text-slate-300 uppercase tracking-widest mb-2">Step <?= $step['num'] ?></p>
                <h3 class="text-lg font-bold text-slate-900 mb-2"><?= $step['title'] ?></h3>
                <p class="text-sm text-slate-500 leading-relaxed"><?= $step['desc'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-12">
            <a href="<?= $base_url ?>/booking"
               class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold text-sm px-8 py-4 rounded-lg transition">
                <i class="fa-regular fa-calendar-check" aria-hidden="true"></i>
                Get Your Free Quote — 60 Seconds
            </a>
        </div>
    </div>
</section>


<!-- ── TESTIMONIALS ──────────────────────────────────────────────── -->
<section class="py-20 lg:py-28 bg-slate-50" aria-labelledby="reviews-heading">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-12">
            <p class="text-xs font-semibold uppercase tracking-widest text-green-600 mb-3">Customer Reviews</p>
            <h2 id="reviews-heading" class="text-3xl lg:text-4xl font-bold text-slate-900 tracking-tight mb-4">
                Trusted by 200+ Homeowners
            </h2>
            <div class="inline-flex items-center gap-2">
                <div class="flex items-center gap-0.5">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118L10 14.347l-3.37 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.644 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69L9.049 2.927z"/>
                    </svg>
                    <?php endfor; ?>
                </div>
                <span class="text-slate-700 font-semibold text-sm">4.9 / 5 on Google</span>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <?php
            $reviews = [
                ['name' => 'Sarah M.', 'area' => 'Chermside, QLD',    'time' => '2 weeks ago',  'service' => 'Bed Bug Treatment',
                 'review' => 'I was in a complete panic when I discovered bed bugs. They came out the same day I called and the treatment worked perfectly. Slept soundly that night for the first time in weeks. Highly recommend.'],
                ['name' => 'David K.', 'area' => 'Eastern Suburbs NSW','time' => '1 month ago',  'service' => 'Rodent Exclusion',
                 'review' => 'Mice in my basement every winter for three years. They found every entry point, sealed them all properly, and I haven\'t seen a single mouse since. Best investment I\'ve made in my home.'],
                ['name' => 'Priya T.', 'area' => 'Carindale, QLD',    'time' => '3 weeks ago', 'service' => 'Cockroach Control',
                 'review' => 'Fast, professional, and genuinely effective. The gel bait worked within days and a follow-up confirmed complete eradication. The technician was thorough and explained everything clearly.'],
            ];
            foreach ($reviews as $r): ?>
            <div class="bg-white rounded-xl border border-slate-200 p-6 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-0.5">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                        <svg class="w-3.5 h-3.5 text-yellow-400 fill-current" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118L10 14.347l-3.37 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.644 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69L9.049 2.927z"/>
                        </svg>
                        <?php endfor; ?>
                    </div>
                    <span class="text-xs text-slate-400"><?= $r['time'] ?></span>
                </div>

                <blockquote class="text-slate-600 text-sm leading-relaxed flex-grow mb-4">"<?= $r['review'] ?>"</blockquote>

                <span class="inline-flex text-xs font-medium text-green-700 bg-green-50 border border-green-100 px-2.5 py-1 rounded-full mb-4 w-fit">
                    <?= $r['service'] ?>
                </span>

                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold text-xs"><?= substr($r['name'], 0, 1) ?></span>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900 text-sm leading-tight"><?= $r['name'] ?></p>
                        <p class="text-xs text-slate-400"><?= $r['area'] ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ── WHY US — Stats (dark) ─────────────────────────────────────── -->
<section class="py-20 lg:py-28 bg-dark" aria-labelledby="why-heading">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">

            <!-- Stats grid -->
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-green-500 mb-3">By the Numbers</p>
                <h2 id="why-heading" class="text-3xl lg:text-4xl font-bold text-white tracking-tight mb-10">
                    Why Sydney &amp; Brisbane<br>Homeowners Choose Us
                </h2>
                <div class="grid grid-cols-2 gap-4">
                    <?php
                    $stats = [
                        ['num' => '500+',  'label' => 'Jobs completed',        'icon' => 'fa-briefcase'],
                        ['num' => '4.9★',  'label' => 'Google rating',         'icon' => 'fa-star'],
                        ['num' => '12mo',  'label' => 'Maximum protection',    'icon' => 'fa-shield-halved'],
                        ['num' => '2hrs',  'label' => 'Average callback time', 'icon' => 'fa-bolt'],
                    ];
                    foreach ($stats as $s): ?>
                    <div class="bg-white/5 border border-white/8 rounded-xl p-5">
                        <i class="fa-solid <?= $s['icon'] ?> text-green-400 text-lg mb-3 block" aria-hidden="true"></i>
                        <p class="text-3xl font-bold text-white leading-none mb-1"><?= $s['num'] ?></p>
                        <p class="text-sm text-slate-400"><?= $s['label'] ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Benefits list -->
            <div>
                <ul class="space-y-6" role="list">
                    <?php
                    $benefits = [
                        ['fa-certificate',   'Licensed Technicians',              'Every technician holds a valid pest management licence — you\'re fully protected by law.'],
                        ['fa-leaf',          'Eco-Friendly, Pet-Safe Treatments', 'Low-toxicity products that protect your family, children, and pets throughout the process.'],
                        ['fa-rotate-left',   'Free Retreatment Commitment',       'If pests return within your service period, we come back and retreat at no cost.'],
                        ['fa-calendar-check','Same-Day &amp; Emergency Service',  'Book before noon and we\'ll often have a licensed technician at your door today.'],
                    ];
                    foreach ($benefits as [$icon, $title, $desc]): ?>
                    <li class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-lg bg-green-600/15 border border-green-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fa-solid <?= $icon ?> text-green-400 text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-white mb-0.5 text-sm"><?= $title ?></p>
                            <p class="text-sm text-slate-400 leading-relaxed"><?= $desc ?></p>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="flex flex-col sm:flex-row gap-3 mt-9">
                    <a href="<?= $base_url ?>/booking"
                       class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3.5 rounded-lg transition text-sm">
                        <i class="fa-regular fa-calendar-check" aria-hidden="true"></i>
                        Book Free Inspection
                    </a>
                    <a href="<?= $base_url ?>/about"
                       class="inline-flex items-center justify-center gap-2 border border-white/15 hover:border-white/25 text-white font-medium px-6 py-3.5 rounded-lg transition text-sm">
                        About Our Team
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ── SERVICE AREAS ─────────────────────────────────────────────── -->
<section class="py-16 lg:py-20 bg-white border-t border-slate-100" aria-labelledby="areas-heading">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-xs font-semibold uppercase tracking-widest text-green-600 mb-3">Coverage Area</p>
        <h2 id="areas-heading" class="text-2xl font-bold text-slate-900 tracking-tight mb-2">
            Serving Sydney &amp; Brisbane
        </h2>
        <p class="text-slate-500 text-sm mb-8 max-w-md mx-auto">
            Same-day and next-day appointments available. 24/7 emergency response.
        </p>
        <div class="flex flex-wrap justify-center gap-2 mb-6">
            <?php
            $areas = ['Sydney CBD','Inner West','Eastern Suburbs','Parramatta','Hills District','Sutherland Shire','Western Sydney','North Shore','Brisbane CBD','Southside Brisbane','North Brisbane','Eastern Brisbane','Logan','Ipswich'];
            foreach ($areas as $area): ?>
            <a href="<?= $base_url ?>/booking"
               class="border border-slate-200 hover:border-green-300 hover:bg-green-50 text-slate-600 hover:text-green-700 text-sm font-medium px-4 py-2 rounded-lg transition">
                <?= $area ?>
            </a>
            <?php endforeach; ?>
        </div>
        <p class="text-slate-400 text-xs">
            Don't see your suburb?
            <a href="<?= $base_url ?>/contact" class="text-green-600 hover:text-green-700 font-medium ml-1 transition">Contact us — we likely serve you.</a>
        </p>
    </div>
</section>


<!-- ── FINAL CTA ─────────────────────────────────────────────────── -->
<section class="bg-dark border-t border-white/5 py-16 lg:py-20">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-xs font-semibold uppercase tracking-widest text-green-500 mb-3">Act Now</p>
        <h2 class="text-3xl lg:text-4xl font-bold text-white tracking-tight mb-4">
            Don't Let Pests Take Over Your Home.
        </h2>
        <p class="text-slate-300 text-lg mb-8 max-w-lg mx-auto">
            Every day you wait, the infestation grows. Get a free inspection before it gets worse.
        </p>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3">
            <a href="<?= $base_url ?>/booking"
               class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-bold text-sm px-8 py-4 rounded-lg transition">
                <i class="fa-regular fa-calendar-check" aria-hidden="true"></i>
                Book Free Inspection
            </a>
            <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
               class="inline-flex items-center justify-center gap-2 border border-white/15 hover:border-white/25 text-white font-semibold text-sm px-8 py-4 rounded-lg transition">
                <i class="fa-solid fa-phone text-xs" aria-hidden="true"></i>
                <?= htmlspecialchars($site_phone) ?>
            </a>
        </div>
        <p class="text-slate-500 text-xs mt-5">
            Licensed &amp; Insured · 100% Satisfaction Assured · No Obligation
        </p>
    </div>
</section>

</main>


<!-- ── MOBILE STICKY BAR ─────────────────────────────────────────── -->
<div class="fixed bottom-0 left-0 right-0 z-50 lg:hidden bg-dark border-t border-white/10 px-4 py-3"
     id="mobile-sticky-bar"
     style="transform:translateY(100%);transition:transform .3s ease">
    <div class="flex items-center gap-3 max-w-md mx-auto">
        <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
           class="w-12 h-12 rounded-lg border border-white/10 bg-white/5 flex items-center justify-center flex-shrink-0"
           aria-label="Call us">
            <i class="fa-solid fa-phone text-green-400" aria-hidden="true"></i>
        </a>
        <a href="<?= $base_url ?>/booking"
           class="flex-grow flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold text-sm py-3 rounded-lg transition">
            <i class="fa-regular fa-calendar-check" aria-hidden="true"></i>
            Book Free Inspection
        </a>
    </div>
</div>
<div class="lg:hidden h-[72px]" aria-hidden="true" id="mobile-spacer" style="display:none"></div>

<script>
(function () {
    var bar    = document.getElementById('mobile-sticky-bar');
    var spacer = document.getElementById('mobile-spacer');
    var form   = document.getElementById('hero-form');
    if (!bar || !form) return;
    if (window.innerWidth >= 1024) return;
    setTimeout(function () {
        spacer.style.display = '';
        bar.style.transform  = 'translateY(0)';
    }, 800);
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            bar.style.transform = entries[0].isIntersecting ? 'translateY(100%)' : 'translateY(0)';
        }, { threshold: 0.5 });
        io.observe(form);
    }
})();
</script>

<?php require_once BASE_DIR . '/includes/footer.php'; ?>
