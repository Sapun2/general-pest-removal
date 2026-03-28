<?php
require_once BASE_DIR . '/includes/db.php';
require_once BASE_DIR . '/includes/seo-meta.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error   = $_SESSION['flash_error']   ?? '';
$form_data     = $_SESSION['form_data']     ?? [];
unset($_SESSION['flash_success'], $_SESSION['flash_error'], $_SESSION['form_data']);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$page_seo = get_page_seo('contact', [
    'title'       => 'Contact General Pest Removal | 24/7 Pest Emergency Line Sydney & Brisbane',
    'description' => 'Contact General Pest Removal for fast, licensed pest removal across Sydney, Brisbane, Eastern Suburbs, Parramatta, and surrounds. Call 24/7 or book online for a free inspection.',
    'canonical'   => SITE_BASE_URL . '/contact',
    'breadcrumbs' => [
        ['name' => 'Home',       'url' => '/'],
        ['name' => 'Contact Us', 'url' => '/contact'],
    ],
    'schema' => [
        '@context'   => 'https://schema.org',
        '@type'      => 'ContactPage',
        'name'       => 'Contact General Pest Removal',
        'url'        => SITE_BASE_URL . '/contact',
        'mainEntity' => ['@type' => 'LocalBusiness', 'name' => SITE_NAME, 'telephone' => SITE_PHONE_RAW, 'email' => SITE_EMAIL],
    ],
]);

require_once BASE_DIR . '/includes/header.php';
?>

<main class="flex-grow bg-slate-50">

    <!-- ── Emergency Banner ─────────────────────────────────────── -->
    <div class="bg-green-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-3 text-white">
                <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-bolt text-xs" aria-hidden="true"></i>
                </div>
                <span class="text-sm font-semibold">Pest emergency? Our 24/7 line is always open.</span>
            </div>
            <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
               class="inline-flex items-center gap-2 bg-white text-green-700 text-sm font-bold px-5 py-2 rounded-lg hover:bg-green-50 transition flex-shrink-0">
                <i class="fa-solid fa-phone text-xs" aria-hidden="true"></i>
                Call <?= htmlspecialchars($site_phone) ?> Now
            </a>
        </div>
    </div>

    <!-- ── HERO ─────────────────────────────────────────────────── -->
    <section class="bg-dark text-white py-16 md:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb" class="mb-7">
                <ol class="flex items-center gap-2 text-xs text-slate-500">
                    <li><a href="<?= $base_url ?>/" class="hover:text-slate-300 transition">Home</a></li>
                    <li aria-hidden="true" class="text-slate-700">/</li>
                    <li aria-current="page" class="text-slate-300">Contact Us</li>
                </ol>
            </nav>
            <div class="grid lg:grid-cols-2 gap-10 items-center">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-green-400 mb-4">Get in Touch</p>
                    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight mb-5 leading-tight">
                        We're Ready When<br>
                        <span class="text-green-400">You Need Us</span>
                    </h1>
                    <p class="text-slate-300 leading-relaxed mb-7 text-lg">
                        Call, email, or fill out the form. Our team responds within 2 hours during business hours — and 24/7 for emergencies.
                    </p>
                    <div class="space-y-3">
                        <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                           class="flex items-center gap-4 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl px-5 py-4 transition group">
                            <div class="w-10 h-10 rounded-lg bg-green-600 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-phone text-white text-sm" aria-hidden="true"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-medium">Call or Text — 24/7 Emergency</p>
                                <p class="text-white font-bold text-xl leading-tight"><?= htmlspecialchars($site_phone) ?></p>
                            </div>
                        </a>
                        <a href="mailto:<?= htmlspecialchars($site_email) ?>"
                           class="flex items-center gap-4 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl px-5 py-4 transition group">
                            <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-envelope text-white text-sm" aria-hidden="true"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-medium">Email — 1 Business Day Response</p>
                                <p class="text-white font-semibold"><?= htmlspecialchars($site_email) ?></p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Hours card -->
                <div class="bg-white/5 border border-white/10 rounded-xl p-7">
                    <h2 class="text-base font-bold text-white mb-5 flex items-center gap-2">
                        <i class="fa-solid fa-clock text-green-400" aria-hidden="true"></i>
                        Hours of Operation
                    </h2>
                    <div class="space-y-3 mb-6">
                        <?php
                        $hours = [
                            ['Mon – Friday',          '7:00 am – 8:00 pm', false],
                            ['Saturday',              '8:00 am – 6:00 pm', false],
                            ['Sunday',                '9:00 am – 5:00 pm', false],
                            ['Emergency / 24hr Line', 'Always Available',   true],
                        ];
                        foreach ($hours as [$day, $time, $emerg]): ?>
                        <div class="flex items-center justify-between py-2 <?= $emerg ? 'border-t border-white/10 mt-1' : '' ?>">
                            <span class="<?= $emerg ? 'text-green-400 font-semibold' : 'text-slate-300' ?> text-sm"><?= $day ?></span>
                            <span class="<?= $emerg ? 'text-green-400 font-bold' : 'text-white font-medium' ?> text-sm"><?= $time ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="<?= $base_url ?>/booking"
                       class="block text-center bg-green-600 hover:bg-green-700 text-white font-semibold py-3.5 rounded-lg transition text-sm">
                        <i class="fa-solid fa-calendar-check mr-2" aria-hidden="true"></i>
                        Book Online — Free Inspection
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ── CONTACT FORM + INFO ───────────────────────────────────── -->
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-8 max-w-6xl mx-auto">

                <!-- Contact Form -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="bg-dark px-7 py-5 border-b border-white/5">
                        <h2 class="text-white font-bold text-base">Send Us a Message</h2>
                        <p class="text-slate-400 text-xs mt-1">For non-urgent inquiries, commercial quotes, or general questions.</p>
                    </div>
                    <div class="p-7">

                        <?php if ($flash_success): ?>
                        <div class="bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-4 rounded-lg mb-6 flex items-start gap-3">
                            <i class="fa-solid fa-circle-check text-green-600 flex-shrink-0 mt-0.5" aria-hidden="true"></i>
                            <p><?= htmlspecialchars($flash_success) ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if ($flash_error): ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3.5 rounded-lg mb-6 flex items-start gap-3">
                            <i class="fa-solid fa-triangle-exclamation flex-shrink-0 mt-0.5" aria-hidden="true"></i>
                            <p><?= htmlspecialchars($flash_error) ?></p>
                        </div>
                        <?php endif; ?>

                        <form action="<?= $base_url ?>/process_contact" method="POST" class="space-y-5" novalidate data-crm="contact">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                            <div class="grid sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="c_name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Full Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="c_name" name="name" required autocomplete="name"
                                           value="<?= htmlspecialchars($form_data['name'] ?? '') ?>"
                                           class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-500/10 outline-none transition text-sm text-slate-900">
                                </div>
                                <div>
                                    <label for="c_phone" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Phone <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel" id="c_phone" name="phone" required autocomplete="tel"
                                           placeholder="(02) 8155 0198"
                                           value="<?= htmlspecialchars($form_data['phone'] ?? '') ?>"
                                           class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-500/10 outline-none transition text-sm text-slate-900 placeholder:text-slate-400">
                                </div>
                            </div>

                            <div>
                                <label for="c_email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="c_email" name="email" required autocomplete="email"
                                       placeholder="you@email.com"
                                       value="<?= htmlspecialchars($form_data['email'] ?? '') ?>"
                                       class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-500/10 outline-none transition text-sm text-slate-900 placeholder:text-slate-400">
                            </div>

                            <div>
                                <label for="c_message" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Message <span class="text-red-500">*</span>
                                </label>
                                <textarea id="c_message" name="message" required rows="5"
                                          placeholder="Tell us about your pest problem or question..."
                                          class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-500/10 outline-none transition text-sm text-slate-900 resize-none placeholder:text-slate-400"><?= htmlspecialchars($form_data['message'] ?? '') ?></textarea>
                            </div>

                            <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3.5 rounded-lg transition flex items-center justify-center gap-2 text-sm">
                                <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Info column -->
                <div class="space-y-6">

                    <!-- Service Areas -->
                    <div class="bg-white rounded-xl border border-slate-200 p-7">
                        <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2 text-sm">
                            <i class="fa-solid fa-map-location-dot text-green-600" aria-hidden="true"></i>
                            Service Areas
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            <?php
                            $areas = ['Sydney CBD','Inner West','Eastern Suburbs','Parramatta','Hills District','Sutherland Shire','North Shore','Western Sydney','Brisbane CBD','Southside Brisbane','North Brisbane','Eastern Brisbane','Logan','Ipswich'];
                            foreach ($areas as $area): ?>
                            <span class="text-xs text-slate-600 border border-slate-200 px-2.5 py-1 rounded-md"><?= $area ?></span>
                            <?php endforeach; ?>
                        </div>
                        <p class="text-xs text-slate-400 mt-4">
                            Don't see your suburb? <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>" class="text-green-600 font-medium hover:underline">Call us</a> — we likely service you.
                        </p>
                    </div>

                    <!-- Commitment cards -->
                    <?php
                    $commits = [
                        ['fa-bolt',       'Rapid Response', 'We call back within 2 hours during business hours. Same-day appointments available.'],
                        ['fa-shield-halved','Licensed & Insured', 'Every technician is NSW & QLD licensed. $5M+ liability insurance on every job.'],
                    ];
                    foreach ($commits as [$icon, $title, $desc]): ?>
                    <div class="bg-white rounded-xl border border-slate-200 p-6 flex items-start gap-4">
                        <div class="w-10 h-10 rounded-lg bg-green-50 border border-green-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid <?= $icon ?> text-green-600 text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-slate-900 text-sm mb-1"><?= $title ?></h4>
                            <p class="text-xs text-slate-500 leading-relaxed"><?= $desc ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <!-- Quick links -->
                    <div class="bg-dark rounded-xl border border-white/8 p-6">
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-4">Quick Actions</p>
                        <div class="space-y-3">
                            <a href="<?= $base_url ?>/booking"
                               class="flex items-center gap-3 text-sm text-white hover:text-green-400 transition font-medium">
                                <i class="fa-regular fa-calendar-check text-green-400" aria-hidden="true"></i>
                                Book a Free Inspection
                            </a>
                            <a href="<?= $base_url ?>/services"
                               class="flex items-center gap-3 text-sm text-slate-300 hover:text-white transition">
                                <i class="fa-solid fa-shield-bug text-slate-500" aria-hidden="true"></i>
                                Browse Our Services
                            </a>
                            <a href="<?= $base_url ?>/faq"
                               class="flex items-center gap-3 text-sm text-slate-300 hover:text-white transition">
                                <i class="fa-solid fa-circle-question text-slate-500" aria-hidden="true"></i>
                                Read Our FAQ
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

</main>

<?php require_once BASE_DIR . '/includes/footer.php'; ?>
