<?php
require_once BASE_DIR . '/includes/db.php';
require_once BASE_DIR . '/includes/seo-meta.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$flash_error = $_SESSION['flash_error'] ?? '';
$form_data   = $_SESSION['form_data']   ?? [];
unset($_SESSION['flash_error'], $_SESSION['form_data']);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$selected_service = '';
if (isset($_GET['service'])) {
    $selected_service = htmlspecialchars(trim($_GET['service']), ENT_QUOTES, 'UTF-8');
}
if (!empty($form_data['pest_type'])) {
    $selected_service = $form_data['pest_type'];
}

$service_labels = [
    'bedbugs'     => 'Bed Bug',
    'rodents'     => 'Rodent Control',
    'cockroaches' => 'Cockroach',
    'ants'        => 'Ant Control',
    'wildlife'    => 'Wildlife Removal',
    'wasps'       => 'Wasp & Hornet',
];
$service_label = !empty($selected_service) && isset($service_labels[$selected_service])
    ? $service_labels[$selected_service] . ' '
    : '';

$page_seo = get_page_seo('booking', [
    'title'       => $service_label . 'Pest Control Booking — Free Inspection & Quote | General Pest Removal',
    'description' => 'Book a free pest control inspection and quote in Sydney, Brisbane, Eastern Suburbs, Parramatta, and surrounds. Termites, rodents, cockroaches — licensed team responds within 2 hours.',
    'canonical'   => SITE_BASE_URL . '/booking',
    'og_title'    => 'Book a Free Pest Inspection | General Pest Removal',
    'noindex'     => true,
    'breadcrumbs' => [
        ['name' => 'Home',        'url' => '/'],
        ['name' => 'Book Online', 'url' => '/booking'],
    ],
]);

require_once BASE_DIR . '/includes/header.php';
?>

<main class="flex-grow bg-slate-50">

    <!-- ── Page Header ──────────────────────────────────────────── -->
    <div class="bg-dark border-b border-white/5 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb" class="mb-4">
                <ol class="flex items-center gap-2 text-xs text-slate-500">
                    <li><a href="<?= $base_url ?>/" class="hover:text-slate-300 transition">Home</a></li>
                    <li aria-hidden="true" class="text-slate-700">/</li>
                    <li aria-current="page" class="text-slate-300">Book Online</li>
                </ol>
            </nav>
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-green-400 mb-2">Free Inspection &amp; Quote</p>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">
                        Request a Free <?= $service_label ?>Quote
                    </h1>
                    <p class="text-slate-400 text-sm mt-2">
                        Our team will contact you within 2 hours during business hours. No obligation.
                    </p>
                </div>
                <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                   class="inline-flex items-center gap-2 border border-green-500/30 bg-green-600/15 text-green-400 font-semibold px-4 py-2.5 rounded-lg transition text-sm flex-shrink-0 hover:bg-green-600/25">
                    <i class="fa-solid fa-phone text-xs" aria-hidden="true"></i>
                    <?= htmlspecialchars($site_phone) ?>
                </a>
            </div>
        </div>
    </div>

    <!-- ── Main Content ─────────────────────────────────────────── -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col lg:flex-row gap-8 max-w-6xl mx-auto">

            <!-- Booking Form -->
            <div class="w-full lg:w-3/5">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

                    <div class="bg-dark px-7 py-5 border-b border-white/5 flex items-center justify-between">
                        <div>
                            <p class="text-white font-bold text-base">Book Your Free Inspection</p>
                            <p class="text-slate-400 text-xs mt-0.5 flex items-center gap-1.5">
                                <i class="fa-regular fa-clock text-green-400" aria-hidden="true"></i>
                                We'll call you back within 2 hours
                            </p>
                        </div>
                        <div class="hidden sm:flex items-center gap-1">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                            <i class="fa-solid fa-star text-yellow-400 text-xs" aria-hidden="true"></i>
                            <?php endfor; ?>
                            <span class="text-slate-400 text-xs ml-1">4.9/5</span>
                        </div>
                    </div>

                    <div class="p-7 md:p-9">

                        <?php if ($flash_error): ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3.5 rounded-lg mb-6 flex items-start gap-3">
                            <i class="fa-solid fa-triangle-exclamation flex-shrink-0 mt-0.5" aria-hidden="true"></i>
                            <p><?= htmlspecialchars($flash_error) ?></p>
                        </div>
                        <?php endif; ?>

                        <form action="<?= $base_url ?>/process_booking" method="POST" class="space-y-5" novalidate data-crm="booking">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="first_name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        First Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="first_name" name="first_name" required autocomplete="given-name"
                                           value="<?= htmlspecialchars($form_data['first_name'] ?? '') ?>"
                                           class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-500/10 outline-none transition text-sm text-slate-900">
                                </div>
                                <div>
                                    <label for="last_name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Last Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="last_name" name="last_name" required autocomplete="family-name"
                                           value="<?= htmlspecialchars($form_data['last_name'] ?? '') ?>"
                                           class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-500/10 outline-none transition text-sm text-slate-900">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="phone" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Phone <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel" id="phone" name="phone" required autocomplete="tel"
                                           placeholder="(02) 8155 0198"
                                           value="<?= htmlspecialchars($form_data['phone'] ?? '') ?>"
                                           class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-500/10 outline-none transition text-sm text-slate-900 placeholder:text-slate-400">
                                </div>
                                <div>
                                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Email <span class="text-slate-400 font-normal">(optional)</span>
                                    </label>
                                    <input type="email" id="email" name="email" autocomplete="email"
                                           placeholder="you@email.com"
                                           value="<?= htmlspecialchars($form_data['email'] ?? '') ?>"
                                           class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-500/10 outline-none transition text-sm text-slate-900 placeholder:text-slate-400">
                                </div>
                            </div>

                            <div>
                                <label for="street_address" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Street Address <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="street_address" name="street_address" required
                                       autocomplete="street-address"
                                       placeholder="123 Main St, Sydney NSW"
                                       value="<?= htmlspecialchars($form_data['street_address'] ?? '') ?>"
                                       class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-500/10 outline-none transition text-sm text-slate-900 placeholder:text-slate-400">
                                <p class="text-xs text-slate-400 mt-1.5">
                                    <i class="fa-solid fa-lock mr-1" aria-hidden="true"></i>
                                    Used only to dispatch the nearest technician to you.
                                </p>
                            </div>

                            <div>
                                <label for="pest_type" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    What's the Pest? <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select id="pest_type" name="pest_type" required
                                            class="w-full appearance-none px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-500/10 outline-none transition text-sm text-slate-900 pr-10">
                                        <option value="">Select the pest type...</option>
                                        <option value="bedbugs"     <?= $selected_service === 'bedbugs'     ? 'selected' : '' ?>>Bed Bugs</option>
                                        <option value="rodents"     <?= $selected_service === 'rodents'     ? 'selected' : '' ?>>Mice &amp; Rats</option>
                                        <option value="cockroaches" <?= $selected_service === 'cockroaches' ? 'selected' : '' ?>>Cockroaches</option>
                                        <option value="ants"        <?= $selected_service === 'ants'        ? 'selected' : '' ?>>Ants &amp; Carpenter Ants</option>
                                        <option value="wasps"       <?= $selected_service === 'wasps'       ? 'selected' : '' ?>>Wasps &amp; Hornets</option>
                                        <option value="wildlife"    <?= $selected_service === 'wildlife'    ? 'selected' : '' ?>>Wildlife</option>
                                        <option value="other">Not Sure / Other</option>
                                    </select>
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                        <i class="fa-solid fa-chevron-down text-xs" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label for="message" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Describe the Problem <span class="text-slate-400 font-normal">(optional)</span>
                                </label>
                                <textarea id="message" name="message" rows="4"
                                          placeholder="e.g., I noticed mouse droppings in the kitchen. I have a dog at home..."
                                          class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-500/10 outline-none transition text-sm text-slate-900 resize-none placeholder:text-slate-400"><?= htmlspecialchars($form_data['message'] ?? '') ?></textarea>
                            </div>

                            <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold text-base py-4 rounded-lg transition flex items-center justify-center gap-2.5">
                                <i class="fa-solid fa-calendar-check" aria-hidden="true"></i>
                                Get My Free Inspection &amp; Quote
                            </button>

                            <div class="flex items-center justify-center gap-6 pt-1">
                                <p class="text-xs text-slate-400 flex items-center gap-1.5">
                                    <i class="fa-solid fa-lock text-slate-300" aria-hidden="true"></i>
                                    Secure &amp; confidential
                                </p>
                                <p class="text-xs text-slate-400 flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-check text-green-500" aria-hidden="true"></i>
                                    No spam, ever
                                </p>
                                <p class="text-xs text-slate-400 flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-check text-green-500" aria-hidden="true"></i>
                                    100% free
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Trust Sidebar -->
            <div class="w-full lg:w-2/5 space-y-5">

                <!-- Why Choose Us -->
                <div class="bg-dark rounded-xl border border-white/8 p-7 text-white">
                    <h2 class="text-sm font-bold mb-5 flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-green-400" aria-hidden="true"></i>
                        Why Customers Choose Us
                    </h2>
                    <div class="space-y-5">
                        <?php
                        $reasons = [
                            ['fa-bolt',        'Fastest Response',         'We call back within 2 hours and dispatch same-day when possible.'],
                            ['fa-certificate', 'Licensed Technicians',     'Every technician holds a valid pest management licence across NSW and QLD.'],
                            ['fa-rotate-left', 'Free Retreatment',         'If pests return within your service period, we come back at no charge.'],
                            ['fa-leaf',        'Eco-Friendly &amp; Pet-Safe', 'Low-impact products safe for your family, kids, and pets.'],
                        ];
                        foreach ($reasons as [$icon, $title, $desc]): ?>
                        <div class="flex items-start gap-3.5">
                            <div class="w-8 h-8 bg-green-600/15 border border-green-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid <?= $icon ?> text-green-400 text-xs" aria-hidden="true"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-sm mb-0.5"><?= $title ?></p>
                                <p class="text-slate-400 text-xs leading-relaxed"><?= $desc ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-6 pt-5 border-t border-white/8">
                        <p class="text-slate-500 text-xs mb-2">Prefer to call?</p>
                        <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                           class="text-lg font-bold text-white hover:text-green-400 transition flex items-center gap-2">
                            <i class="fa-solid fa-phone text-green-400 text-sm" aria-hidden="true"></i>
                            <?= htmlspecialchars($site_phone) ?>
                        </a>
                        <p class="text-green-400 text-xs mt-1.5">24/7 emergency line available</p>
                    </div>
                </div>

                <!-- Review snippet -->
                <div class="bg-white rounded-xl border border-slate-200 p-6">
                    <div class="flex items-center gap-0.5 mb-3">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                        <i class="fa-solid fa-star text-yellow-400 text-sm" aria-hidden="true"></i>
                        <?php endfor; ?>
                        <span class="ml-2 text-xs text-slate-500">4.9/5 · 200+ Reviews</span>
                    </div>
                    <p class="text-slate-600 text-sm leading-relaxed italic mb-4">
                        "Called at 8am, technician arrived by noon. Complete elimination in one visit. Professional, discreet, and genuinely effective. Would call again without hesitation."
                    </p>
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold text-xs">M</span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-900">Michael R.</p>
                            <p class="text-xs text-slate-400">Sydney · Termite Inspection</p>
                        </div>
                        <img src="https://www.google.com/favicon.ico" alt="Google" class="w-4 h-4 ml-auto opacity-50">
                    </div>
                </div>

                <!-- Quick service links -->
                <div class="bg-white rounded-xl border border-slate-200 p-6">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-3">Booking for a specific pest?</p>
                    <div class="flex flex-wrap gap-2">
                        <?php
                        $quick_links = [
                            ['service=bedbugs',     'fa-fire',       'Bed Bugs'],
                            ['service=rodents',     'fa-house-crack','Rodents'],
                            ['service=cockroaches', 'fa-bug',        'Cockroaches'],
                            ['service=wasps',       'fa-circle-dot', 'Wasps'],
                        ];
                        foreach ($quick_links as [$qs, $icon, $label]): ?>
                        <a href="<?= $base_url ?>/booking?<?= $qs ?>"
                           class="inline-flex items-center gap-1.5 text-xs font-medium border border-slate-200 text-slate-600 hover:border-green-500 hover:text-green-700 px-3 py-1.5 rounded-lg transition">
                            <i class="fa-solid <?= $icon ?> text-[10px]" aria-hidden="true"></i>
                            <?= $label ?>
                        </a>
                        <?php endforeach; ?>
                        <a href="<?= $base_url ?>/faq"
                           class="inline-flex items-center gap-1.5 text-xs font-medium border border-green-200 text-green-700 hover:bg-green-50 px-3 py-1.5 rounded-lg transition">
                            <i class="fa-solid fa-circle-question text-[10px]" aria-hidden="true"></i>
                            Read FAQ
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<?php require_once BASE_DIR . '/includes/footer.php'; ?>
