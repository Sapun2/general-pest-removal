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
            'aggregateRating' => ['@type' => 'AggregateRating', 'ratingValue' => '4.8', 'reviewCount' => '186'],
        ]],
    ],
]);

require_once BASE_DIR . '/includes/header.php';
?>

<main class="flex-grow">

<!-- ================================================================
     HERO
     Dark section — headline left, booking form right.
     On mobile the form stacks first (order-first).
================================================================ -->
<section class="relative bg-dark overflow-hidden" aria-label="Hero">

    <!-- Background: photography + directional gradient -->
    <div class="absolute inset-0" aria-hidden="true">
        <img src="<?= $base_url ?>/assets/images/1.png"
             alt=""
             class="w-full h-full object-cover opacity-[0.18]"
             loading="eager">
        <div class="absolute inset-0 bg-gradient-to-br from-dark via-dark/90 to-primary/25"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-20">
        <div class="grid lg:grid-cols-[1fr_420px] xl:grid-cols-[1fr_460px] gap-8 lg:gap-12 items-center">

            <!-- ── Headline & trust ── -->
            <div class="order-2 lg:order-1">

                <!-- Social proof badge -->
                <div class="inline-flex items-center gap-2.5 mb-6">
                    <div class="flex items-center gap-0.5">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118L10 14.347l-3.37 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.644 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69L9.049 2.927z"/>
                        </svg>
                        <?php endfor; ?>
                    </div>
                    <span class="text-white font-semibold text-sm">4.8</span>
                    <span class="text-gray-400 text-sm leading-none">·&nbsp;186 verified Google reviews</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-[52px] xl:text-6xl font-extrabold text-white leading-[1.08] tracking-tight mb-5">
                    Australia's Most Trusted<br>
                    Pest Removal Specialists.<br>
                    <span class="text-secondary">Proven Results.</span>
                </h1>

                <p class="text-gray-300 text-lg leading-relaxed mb-8 max-w-lg">
                    Cockroaches, termites, spiders, ants, rodents — eliminated fast. Licensed technicians, same-day response across Sydney &amp; Brisbane.
                </p>

                <!-- Trust bullets -->
                <ul class="space-y-3 mb-9" role="list">
                    <?php
                    $bullets = [
                        ['fa-bolt-lightning', 'text-yellow-400', 'Same-day & emergency appointments available'],
                        ['fa-shield-halved',  'text-secondary',  'Licensed &amp; fully insured technicians'],
                        ['fa-rotate-left',    'text-secondary',  'Written service report — we come back free if pests return'],
                        ['fa-lock',           'text-green-400',   'Discreet, unmarked vehicles — complete privacy'],
                    ];
                    foreach ($bullets as [$icon, $color, $text]): ?>
                    <li class="flex items-center gap-3 text-gray-300 text-sm">
                        <span class="w-7 h-7 rounded-lg bg-white/8 border border-white/10 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid <?= $icon ?> <?= $color ?> text-[11px]" aria-hidden="true"></i>
                        </span>
                        <?= $text ?>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Phone CTA -->
                <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                   class="inline-flex items-center gap-3 border border-white/15 hover:border-white/30 bg-white/6 hover:bg-white/10 text-white font-semibold px-5 py-3 rounded-xl transition text-sm">
                    <span class="w-8 h-8 rounded-full bg-secondary flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-phone text-white text-xs" aria-hidden="true"></i>
                    </span>
                    <span>
                        <span class="block text-[10px] text-gray-400 font-normal leading-none mb-0.5">24/7 Emergency Line</span>
                        <?= htmlspecialchars($site_phone) ?>
                    </span>
                </a>

                <!-- Compact stat row (desktop only) -->
                <div class="hidden lg:flex items-center gap-6 mt-8 pt-7 border-t border-white/8">
                    <?php foreach (['500+' => 'Jobs completed', '12mo' => 'Max protection', '30 min' => 'Avg. callback', '100%' => 'Satisfaction rate'] as $num => $label): ?>
                    <div>
                        <p class="text-2xl font-extrabold text-white leading-none"><?= $num ?></p>
                        <p class="text-xs text-gray-400 mt-0.5"><?= $label ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ── Booking form card ── -->
            <div class="order-1 lg:order-2 w-full" id="booking-form-anchor">
                <div class="bg-white rounded-2xl overflow-hidden shadow-xl border-t-[3px] border-accent">

                    <!-- Card heading -->
                    <div class="px-6 pt-5 pb-4 border-b border-gray-100">
                        <h2 class="text-[18px] font-bold text-gray-900 leading-tight">Book a Free Inspection</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            <i class="fa-regular fa-clock mr-1" aria-hidden="true"></i>
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

                        <form action="<?= $base_url ?>/process_booking" method="POST" class="space-y-3.5" novalidate id="hero-form">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                            <!-- Name row -->
                            <div class="grid grid-cols-2 gap-3">
                                <?php foreach ([['h_first_name','first_name','given-name','First Name','Jane'], ['h_last_name','last_name','family-name','Last Name','Smith']] as [$id,$name,$ac,$label,$ph]): ?>
                                <div>
                                    <label for="<?= $id ?>" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                        <?= $label ?> <span class="text-accent">*</span>
                                    </label>
                                    <input type="text" id="<?= $id ?>" name="<?= $name ?>" required
                                           autocomplete="<?= $ac ?>" placeholder="<?= $ph ?>"
                                           value="<?= htmlspecialchars($form_data[$name] ?? '') ?>"
                                           class="w-full px-3.5 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/10 text-sm text-gray-900 placeholder:text-gray-400 transition font-medium">
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="h_phone" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Phone Number <span class="text-accent">*</span>
                                </label>
                                <input type="tel" id="h_phone" name="phone" required
                                       autocomplete="tel" placeholder="(07) 3155 0100"
                                       value="<?= htmlspecialchars($form_data['phone'] ?? '') ?>"
                                       class="w-full px-3.5 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/10 text-sm text-gray-900 placeholder:text-gray-400 transition font-medium">
                            </div>

                            <!-- Pest Type -->
                            <div>
                                <label for="h_pest_type" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    What Pest? <span class="text-accent">*</span>
                                </label>
                                <div class="relative">
                                    <select id="h_pest_type" name="pest_type" required
                                            class="w-full appearance-none px-3.5 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/10 text-sm text-gray-900 transition font-medium pr-10">
                                        <option value="">Select pest type...</option>
                                        <option value="bedbugs"     <?= $selected_service === 'bedbugs'     ? 'selected' : '' ?>>Bed Bugs</option>
                                        <option value="rodents"     <?= $selected_service === 'rodents'     ? 'selected' : '' ?>>Mice &amp; Rats</option>
                                        <option value="cockroaches" <?= $selected_service === 'cockroaches' ? 'selected' : '' ?>>Cockroaches</option>
                                        <option value="ants"        <?= $selected_service === 'ants'        ? 'selected' : '' ?>>Ants</option>
                                        <option value="wasps"       <?= $selected_service === 'wasps'       ? 'selected' : '' ?>>Wasps &amp; Hornets</option>
                                        <option value="wildlife"    <?= $selected_service === 'wildlife'    ? 'selected' : '' ?>>Wildlife</option>
                                        <option value="other">Not Sure / Other</option>
                                    </select>
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                        <i class="fa-solid fa-chevron-down text-xs" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Street Address -->
                            <div>
                                <label for="h_street_address" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Street Address <span class="text-accent">*</span>
                                </label>
                                <input type="text" id="h_street_address" name="street_address" required
                                       autocomplete="street-address"
                                       placeholder="123 Main St, Sydney, NSW"
                                       value="<?= htmlspecialchars($form_data['street_address'] ?? '') ?>"
                                       class="w-full px-3.5 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/10 text-sm text-gray-900 placeholder:text-gray-400 transition font-medium">
                            </div>

                            <!-- Submit -->
                            <button type="submit"
                                    class="w-full bg-accent hover:bg-yellow-800 text-white font-bold text-[15px] py-3.5 rounded-xl transition flex items-center justify-center gap-2 mt-1">
                                <i class="fa-regular fa-calendar-check" aria-hidden="true"></i>
                                Request Free Quote
                            </button>
                        </form>

                        <!-- Below-form trust line -->
                        <p class="text-center text-xs text-gray-400 mt-3.5 flex items-center justify-center gap-3">
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-lock text-gray-300 text-[10px]" aria-hidden="true"></i>
                                Secure &amp; private
                            </span>
                            <span class="w-px h-3 bg-gray-200 inline-block"></span>
                            <span>No credit card required</span>
                            <span class="w-px h-3 bg-gray-200 inline-block"></span>
                            <span>100% free</span>
                        </p>
                    </div>
                </div>

                <!-- Mobile trust pills below card -->
                <div class="flex items-center justify-center gap-4 mt-3 lg:hidden">
                    <span class="flex items-center gap-1.5 text-[11px] text-gray-400">
                        <i class="fa-solid fa-shield-halved text-secondary text-[10px]" aria-hidden="true"></i>
                        Licensed
                    </span>
                    <span class="w-px h-3 bg-gray-600 inline-block"></span>
                    <span class="flex items-center gap-1.5 text-[11px] text-gray-400">
                        <i class="fa-solid fa-rotate-left text-secondary text-[10px]" aria-hidden="true"></i>
                        Free Retreatment
                    </span>
                    <span class="w-px h-3 bg-gray-600 inline-block"></span>
                    <span class="flex items-center gap-1.5 text-[11px] text-gray-400">
                        <i class="fa-solid fa-bolt text-yellow-400 text-[10px]" aria-hidden="true"></i>
                        Same-Day
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ================================================================
     TRUST METRICS STRIP
================================================================ -->
<section class="bg-white border-b border-gray-100" aria-label="Trust signals">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 divide-x divide-y lg:divide-y-0 divide-gray-100">
            <?php
            $trust_metrics = [
                ['icon' => 'fa-star',        'color' => 'text-yellow-500', 'num' => '4.9 / 5',   'label' => '200+ verified Google reviews'],
                ['icon' => 'fa-bolt',         'color' => 'text-accent',     'num' => '30 min',    'label' => 'Average callback during business hours'],
                ['icon' => 'fa-rotate-left',  'color' => 'text-secondary',  'num' => 'Free',      'label' => 'Re-treatment if pests return'],
                ['icon' => 'fa-id-badge',     'color' => 'text-primary',    'num' => 'Licensed',      'label' => 'Licensed &amp; fully insured technicians'],
            ];
            foreach ($trust_metrics as $m): ?>
            <div class="flex items-center gap-3.5 px-5 py-5">
                <div class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid <?= $m['icon'] ?> <?= $m['color'] ?> text-lg" aria-hidden="true"></i>
                </div>
                <div>
                    <p class="font-extrabold text-gray-900 text-sm leading-tight"><?= $m['num'] ?></p>
                    <p class="text-xs text-gray-500 mt-0.5 leading-snug"><?= $m['label'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ================================================================
     SERVICES
================================================================ -->
<section class="py-20 lg:py-28 bg-gray-50" aria-labelledby="services-heading">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center max-w-xl mx-auto mb-14">
            <p class="text-xs font-semibold uppercase tracking-widest text-secondary mb-3">What We Treat</p>
            <h2 id="services-heading" class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight mb-4">
                Specialized Solutions for Every Pest
            </h2>
            <p class="text-gray-500 text-sm leading-relaxed">
                Science-based, environmentally responsible methods — complete eradication backed by a written service report.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            $services = [
                [
                    'img'        => '/assets/images/2.png',
                    'alt'        => 'Termite inspection Sydney & Brisbane',
                    'badge'      => 'Most Requested',
                    'badge_cls'  => 'bg-red-50 text-red-600 border border-red-100',
                    'title'      => 'Termite Inspection &amp; Treatment',
                    'desc'       => 'AS 3660.2-compliant thermal and chemical barrier treatments — complete termite eradication with a written service report.',
                    'features'   => ['AS 3660.2 Australian Standard compliant', 'Same-day emergency service available', 'Written treatment report provided'],
                    'guarantee'  => '12-Month Protection',
                    'book_href'  => '/booking?service=termites',
                    'book_label' => 'Book Inspection',
                    'learn_href' => '/services#termites',
                ],
                [
                    'img'        => '/assets/images/3.png',
                    'alt'        => 'Cockroach control Sydney & Brisbane',
                    'badge'      => 'Year-Round Service',
                    'badge_cls'  => 'bg-yellow-50 text-green-700 border border-green-100',
                    'title'      => 'Cockroach Control',
                    'desc'       => 'IPM-based gel baiting that eliminates the entire colony at the source — safe for kitchens, restaurants, and family homes.',
                    'features'   => ['Targets eggs and the colony queen', 'Safe for food-preparation environments', 'Commercial service contracts available'],
                    'guarantee'  => '6-Month Protection',
                    'book_href'  => '/booking?service=cockroaches',
                    'book_label' => 'Book Inspection',
                    'learn_href' => '/services#cockroaches',
                ],
                [
                    'img'        => '/assets/images/4.png',
                    'alt'        => 'Spider removal Sydney & Brisbane — Redback and Funnel-web',
                    'badge'      => 'Residential &amp; Commercial',
                    'badge_cls'  => 'bg-yellow-50 text-green-700 border border-green-100',
                    'title'      => 'Spider &amp; Ant Removal',
                    'desc'       => 'Safe removal of Redback, Funnel-web, and common spiders plus targeted ant treatments — protecting your family year-round.',
                    'features'   => ['Redback &amp; Funnel-web specialists', 'Pet and child-safe treatments', 'Fire Ant biosecurity compliant'],
                    'guarantee'  => '3-Month Protection',
                    'book_href'  => '/booking?service=spiders',
                    'book_label' => 'Book Inspection',
                    'learn_href' => '/services#spiders',
                ],
            ];
            foreach ($services as $s): ?>
            <article class="bg-white rounded-2xl border border-gray-100 overflow-hidden group hover:shadow-lg transition-shadow duration-300 flex flex-col">
                <div class="relative aspect-[16/9] overflow-hidden">
                    <img src="<?= $base_url . $s['img'] ?>"
                         alt="<?= $s['alt'] ?>"
                         class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500"
                         loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-dark/50 to-transparent"></div>
                    <span class="absolute top-3 left-3 text-xs font-semibold px-2.5 py-1 rounded-full <?= $s['badge_cls'] ?>">
                        <?= $s['badge'] ?>
                    </span>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-lg font-extrabold text-gray-900 mb-2"><?= $s['title'] ?></h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-4"><?= $s['desc'] ?></p>

                    <ul class="space-y-1.5 mb-5" role="list">
                        <?php foreach ($s['features'] as $f): ?>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <i class="fa-solid fa-check text-secondary flex-shrink-0" style="font-size:11px" aria-hidden="true"></i>
                            <?= $f ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="mt-auto flex items-center gap-2 pt-4 border-t border-gray-50">
                        <!-- Protection badge -->
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-secondary bg-secondary/8 px-2.5 py-1 rounded-full flex-shrink-0">
                            <i class="fa-solid fa-shield-halved text-[10px]" aria-hidden="true"></i>
                            <?= $s['guarantee'] ?>
                        </span>
                        <div class="flex items-center gap-2 ml-auto">
                            <a href="<?= $base_url . $s['learn_href'] ?>"
                               class="text-xs font-semibold text-gray-400 hover:text-primary transition px-2 py-1">
                                Details
                            </a>
                            <a href="<?= $base_url . $s['book_href'] ?>"
                               class="text-sm font-bold text-white bg-accent hover:bg-yellow-800 px-4 py-2 rounded-xl transition flex items-center gap-1.5">
                                <i class="fa-regular fa-calendar-check text-xs" aria-hidden="true"></i>
                                <?= $s['book_label'] ?>
                            </a>
                        </div>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <p class="text-center mt-8">
            <a href="<?= $base_url ?>/services"
               class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-primary border border-gray-200 hover:border-primary/40 bg-white px-6 py-3 rounded-xl transition shadow-sm">
                View all services &amp; pricing
                <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
            </a>
        </p>
    </div>
</section>


<!-- ================================================================
     HOW IT WORKS
================================================================ -->
<section class="py-20 lg:py-28 bg-white" aria-labelledby="process-heading">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-14">
            <p class="text-xs font-semibold uppercase tracking-widest text-secondary mb-3">Simple Process</p>
            <h2 id="process-heading" class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight">
                Pest-Free in 3 Steps
            </h2>
        </div>

        <!-- Desktop: horizontal with connector. Mobile: vertical card list. -->
        <div class="relative">
            <!-- Connector line — desktop only -->
            <div class="hidden lg:block absolute top-[38px] left-[calc(16.66%+28px)] right-[calc(16.66%+28px)] h-px bg-gray-200 z-0" aria-hidden="true"></div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-8">
                <?php
                $steps = [
                    ['num' => '1', 'icon' => 'fa-clipboard-list',    'color' => 'bg-primary',   'title' => 'Fill the Quick Form',  'desc' => 'Takes under 60 seconds. Your name, phone number, address, and the pest you\'re dealing with.'],
                    ['num' => '2', 'icon' => 'fa-phone-volume',       'color' => 'bg-secondary', 'title' => 'We Call You Back',     'desc' => 'A licensed technician calls within 2 hours to confirm your appointment and answer any questions.'],
                    ['num' => '3', 'icon' => 'fa-house-circle-check', 'color' => 'bg-accent',    'title' => 'Problem Solved',       'desc' => 'We treat your property and provide a written service report. If pests return, we retreat at no charge.'],
                ];
                foreach ($steps as $step): ?>
                <div class="relative z-10 flex lg:flex-col items-start lg:items-center lg:text-center gap-4 lg:gap-0 bg-gray-50 lg:bg-transparent rounded-2xl lg:rounded-none p-5 lg:p-0">
                    <div class="w-14 h-14 lg:w-16 lg:h-16 <?= $step['color'] ?> rounded-2xl flex items-center justify-center flex-shrink-0 lg:mb-5 shadow-md">
                        <i class="fa-solid <?= $step['icon'] ?> text-white text-xl" aria-hidden="true"></i>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-300 uppercase tracking-widest mb-1 lg:mb-2">Step <?= $step['num'] ?></span>
                        <h3 class="text-base lg:text-lg font-extrabold text-gray-900 mb-1 lg:mb-2"><?= $step['title'] ?></h3>
                        <p class="text-sm text-gray-500 leading-relaxed"><?= $step['desc'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="text-center mt-12">
            <a href="#booking-form-anchor"
               onclick="window.scrollTo({top:0,behavior:'smooth'}); setTimeout(function(){document.getElementById('h_first_name').focus();},500); return false;"
               class="inline-flex items-center gap-2 bg-accent hover:bg-yellow-800 text-white font-bold text-sm px-8 py-4 rounded-xl transition shadow-md">
                <i class="fa-regular fa-calendar-check" aria-hidden="true"></i>
                Get Your Free Quote — Takes 60 Seconds
            </a>
        </div>
    </div>
</section>


<!-- ================================================================
     TESTIMONIALS
================================================================ -->
<section class="py-20 lg:py-28 bg-gray-50" aria-labelledby="reviews-heading">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-12">
            <p class="text-xs font-semibold uppercase tracking-widest text-secondary mb-3">Real Customer Reviews</p>
            <h2 id="reviews-heading" class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight mb-3">
                Trusted by 200+ Homeowners Across Sydney &amp; Brisbane
            </h2>
            <div class="inline-flex items-center gap-2">
                <div class="flex items-center gap-0.5">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118L10 14.347l-3.37 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.644 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69L9.049 2.927z"/>
                    </svg>
                    <?php endfor; ?>
                </div>
                <span class="text-gray-700 font-semibold text-sm">4.9 / 5</span>
                <span class="text-gray-400 text-sm">on Google</span>
            </div>
        </div>

        <!-- Horizontal scroll on mobile, 3-col grid on desktop -->
        <div class="flex lg:grid lg:grid-cols-3 gap-5 overflow-x-auto pb-4 lg:pb-0 lg:overflow-visible -mx-4 px-4 lg:mx-0 lg:px-0 snap-x snap-mandatory">
            <?php
            $reviews = [
                ['name' => 'Sarah M.', 'area' => 'Chermside',  'time' => '2 weeks ago', 'service' => 'Bed Bug Heat Treatment',
                 'review' => 'I was in a complete panic when I discovered bed bugs in my condo. They came out the same day I called and the heat treatment worked perfectly. I slept soundly that night for the first time in weeks. Highly recommend.'],
                ['name' => 'David K.', 'area' => 'Eastern Suburbs', 'time' => '1 month ago',  'service' => 'Rodent Exclusion',
                 'review' => 'Mice in my basement every winter for three years. General Pest Removal found every entry point, sealed them all properly, and I haven\'t seen a single mouse since. Best investment I\'ve made in my home.'],
                ['name' => 'Priya T.', 'area' => 'Carindale', 'time' => '3 weeks ago', 'service' => 'Cockroach Treatment',
                 'review' => 'Fast, professional, and genuinely effective. The gel bait treatment worked within days and a follow-up visit confirmed complete eradication. The technician was thorough and took the time to explain everything.'],
            ];
            foreach ($reviews as $r): ?>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col flex-shrink-0 w-[78vw] sm:w-[58vw] lg:w-auto snap-start">
                <!-- Stars + Google logo -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-0.5">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                        <svg class="w-[14px] h-[14px] text-yellow-400 fill-current" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118L10 14.347l-3.37 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.644 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69L9.049 2.927z"/>
                        </svg>
                        <?php endfor; ?>
                    </div>
                    <img src="https://www.google.com/favicon.ico" alt="Google" class="w-4 h-4 opacity-40">
                </div>

                <!-- Quote text -->
                <blockquote class="text-gray-600 text-sm leading-relaxed flex-grow mb-4">"<?= $r['review'] ?>"</blockquote>

                <!-- Service tag -->
                <span class="inline-flex text-xs font-semibold text-secondary bg-secondary/8 px-2.5 py-1 rounded-full mb-4 w-fit">
                    <?= $r['service'] ?>
                </span>

                <!-- Reviewer -->
                <div class="flex items-center gap-3 pt-4 border-t border-gray-50">
                    <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold text-xs" aria-hidden="true"><?= substr($r['name'], 0, 1) ?></span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm leading-tight"><?= $r['name'] ?></p>
                        <p class="text-xs text-gray-400"><?= $r['area'] ?> &middot; <?= $r['time'] ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <p class="text-center text-xs text-gray-400 mt-4 lg:hidden">Swipe to read more reviews</p>
    </div>
</section>


<!-- ================================================================
     WHY GENERAL PEST REMOVAL — Stats + Benefits (dark)
================================================================ -->
<section class="py-20 lg:py-28 bg-dark" aria-labelledby="why-heading">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">

            <!-- Stats -->
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-secondary mb-3">By the Numbers</p>
                <h2 id="why-heading" class="text-3xl lg:text-4xl font-extrabold text-white tracking-tight mb-10">
                    Why Sydney &amp; Brisbane Homeowners<br>Choose Us
                </h2>
                <div class="grid grid-cols-2 gap-4">
                    <?php
                    $stats = [
                        ['num' => '500+', 'label' => 'Jobs completed',       'icon' => 'fa-briefcase',     'color' => 'text-secondary'],
                        ['num' => '4.9',  'label' => 'Google rating',        'icon' => 'fa-star',          'color' => 'text-yellow-400'],
                        ['num' => '12mo', 'label' => 'Maximum protection',   'icon' => 'fa-shield-halved', 'color' => 'text-secondary'],
                        ['num' => '30m',  'label' => 'Average callback time','icon' => 'fa-bolt',          'color' => 'text-accent'],
                    ];
                    foreach ($stats as $s): ?>
                    <div class="bg-white/5 border border-white/8 rounded-2xl p-5 hover:bg-white/8 transition">
                        <i class="fa-solid <?= $s['icon'] ?> <?= $s['color'] ?> text-lg mb-3 block" aria-hidden="true"></i>
                        <p class="text-3xl font-extrabold text-white leading-none mb-1"><?= $s['num'] ?></p>
                        <p class="text-sm text-gray-400"><?= $s['label'] ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Benefits -->
            <div>
                <ul class="space-y-6" role="list">
                    <?php
                    $benefits = [
                        ['icon' => 'fa-certificate',   'color' => 'bg-secondary/15 text-secondary', 'title' => 'Licensed Technicians',        'desc' => 'Every technician holds a valid pest management licence — you\'re fully protected by law.'],
                        ['icon' => 'fa-leaf',           'color' => 'bg-secondary/15 text-secondary', 'title' => 'Eco-Friendly, Pet-Safe Treatments', 'desc' => 'Heat treatments and low-toxicity products that protect your family, children, and pets throughout the process.'],
                        ['icon' => 'fa-rotate-left',    'color' => 'bg-accent/15 text-accent',       'title' => 'Free Retreatment Commitment',       'desc' => 'If the pest problem returns within your service period, we come back and retreat your property at no cost.'],
                        ['icon' => 'fa-calendar-check', 'color' => 'bg-primary/30 text-green-200',    'title' => 'Same-Day &amp; Emergency Service',  'desc' => 'We understand urgency. Book before noon and we will often have a licensed technician at your door today.'],
                    ];
                    foreach ($benefits as $b): ?>
                    <li class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl <?= $b['color'] ?> flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fa-solid <?= $b['icon'] ?> text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-white mb-0.5"><?= $b['title'] ?></p>
                            <p class="text-sm text-gray-400 leading-relaxed"><?= $b['desc'] ?></p>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="flex flex-col sm:flex-row gap-3 mt-9">
                    <a href="<?= $base_url ?>/booking"
                       class="inline-flex items-center justify-center gap-2 bg-accent hover:bg-yellow-800 text-white font-bold px-6 py-3.5 rounded-xl transition text-sm">
                        <i class="fa-regular fa-calendar-check" aria-hidden="true"></i>
                        Book Free Inspection
                    </a>
                    <a href="<?= $base_url ?>/about"
                       class="inline-flex items-center justify-center gap-2 bg-white/8 hover:bg-white/15 text-white font-semibold px-6 py-3.5 rounded-xl transition text-sm border border-white/10">
                        About Our Team
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ================================================================
     SERVICE AREAS
================================================================ -->
<section class="py-14 lg:py-20 bg-dark" aria-labelledby="areas-heading">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-xs font-semibold uppercase tracking-widest text-secondary mb-3">Coverage Area</p>
        <h2 id="areas-heading" class="text-2xl lg:text-3xl font-extrabold text-white tracking-tight mb-3">
            Serving Sydney &amp; Brisbane
        </h2>
        <p class="text-gray-300 text-sm mb-7 max-w-lg mx-auto">
            Same-day and next-day appointments across Greater Sydney and Greater Brisbane. Emergency response available 24/7.
        </p>
        <div class="flex flex-wrap justify-center gap-2 mb-6">
            <?php
            $areas = ['Sydney CBD','Inner West','Eastern Suburbs','Parramatta','Hills District','Sutherland Shire','Western Sydney','North Shore','Brisbane CBD','Southside Brisbane','North Brisbane','Eastern Brisbane','Logan','Ipswich'];
            foreach ($areas as $area): ?>
            <a href="<?= $base_url ?>/booking"
               class="bg-white/10 hover:bg-white/20 border border-white/15 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                <?= $area ?>
            </a>
            <?php endforeach; ?>
        </div>
        <p class="text-gray-400 text-xs">
            Don't see your city?
            <a href="<?= $base_url ?>/contact" class="text-white underline underline-offset-2 hover:text-secondary ml-1 transition">Contact us — we likely serve you.</a>
        </p>
    </div>
</section>


<!-- ================================================================
     FINAL CTA
================================================================ -->
<section class="bg-accent py-16 lg:py-20">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl lg:text-4xl font-extrabold text-white tracking-tight mb-4">
            Don't Let Pests Take Over Your Home.
        </h2>
        <p class="text-green-100 text-lg mb-8 max-w-lg mx-auto">
            Every day you wait, the infestation grows. Get a free inspection before it gets worse.
        </p>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3">
            <a href="<?= $base_url ?>/booking"
               class="inline-flex items-center justify-center gap-2 bg-white text-accent hover:bg-yellow-50 font-extrabold text-base px-8 py-4 rounded-xl transition shadow-lg">
                <i class="fa-regular fa-calendar-check" aria-hidden="true"></i>
                Book Free Inspection
            </a>
            <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
               class="inline-flex items-center justify-center gap-2 bg-dark/25 hover:bg-dark/40 border border-white/20 text-white font-bold text-base px-8 py-4 rounded-xl transition">
                <i class="fa-solid fa-phone text-sm" aria-hidden="true"></i>
                <?= htmlspecialchars($site_phone) ?>
            </a>
        </div>
        <p class="text-green-200 text-xs mt-5">
            NSW Licensed &amp; Insured &middot; 100% Satisfaction Assured &middot; No Obligation
        </p>
    </div>
</section>

</main>


<!-- ================================================================
     STICKY MOBILE BOTTOM BAR
     Hidden when the booking form is in the viewport.
================================================================ -->
<div class="fixed bottom-0 left-0 right-0 z-50 lg:hidden bg-dark border-t border-white/8 px-4 py-3 shadow-2xl"
     id="mobile-sticky-bar"
     style="transform:translateY(100%);transition:transform .3s ease">
    <div class="flex items-center gap-3 max-w-md mx-auto">
        <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
           class="w-12 h-12 rounded-xl bg-white/8 border border-white/10 flex items-center justify-center flex-shrink-0 hover:bg-white/15 transition"
           aria-label="Call us">
            <i class="fa-solid fa-phone text-secondary" aria-hidden="true"></i>
        </a>
        <a href="#booking-form-anchor"
           onclick="window.scrollTo({top:0,behavior:'smooth'}); setTimeout(function(){document.getElementById('h_first_name').focus();},500); return false;"
           class="flex-grow flex items-center justify-center gap-2 bg-accent hover:bg-yellow-800 text-white font-bold text-sm py-3 rounded-xl transition">
            <i class="fa-regular fa-calendar-check" aria-hidden="true"></i>
            Book Free Inspection
        </a>
    </div>
</div>
<!-- Spacer so sticky bar doesn't cover content on mobile -->
<div class="lg:hidden h-[72px]" aria-hidden="true" id="mobile-spacer" style="display:none"></div>

<script>
(function () {
    var bar    = document.getElementById('mobile-sticky-bar');
    var spacer = document.getElementById('mobile-spacer');
    var form   = document.getElementById('hero-form');
    if (!bar || !form) return;
    if (window.innerWidth >= 1024) return; // desktop: bar never shown

    // Show the spacer and bar after a brief delay on load
    setTimeout(function () {
        spacer.style.display = '';
        bar.style.transform  = 'translateY(0)';
    }, 800);

    // Hide bar when form is visible in viewport
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            bar.style.transform = entries[0].isIntersecting ? 'translateY(100%)' : 'translateY(0)';
        }, { threshold: 0.5 });
        io.observe(form);
    }
})();
</script>

<?php require_once BASE_DIR . '/includes/footer.php'; ?>
