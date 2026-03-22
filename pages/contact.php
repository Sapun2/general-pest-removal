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
        '@context'    => 'https://schema.org',
        '@type'       => 'ContactPage',
        'name'        => 'Contact General Pest Removal',
        'url'         => SITE_BASE_URL . '/contact',
        'mainEntity'  => ['@type' => 'LocalBusiness', 'name' => SITE_NAME, 'telephone' => SITE_PHONE_RAW, 'email' => SITE_EMAIL],
    ],
]);

require_once BASE_DIR . '/includes/header.php';
?>

<main class="flex-grow bg-gray-50">

    <!-- ═══════════════════════════════════════════════════════
         EMERGENCY BANNER
    ══════════════════════════════════════════════════════════ -->
    <div class="bg-accent">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-3 text-white">
                <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-xs" aria-hidden="true"></i>
                </div>
                <span class="text-sm font-semibold">Pest emergency? Our 24/7 line is always open.</span>
            </div>
            <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
               class="inline-flex items-center gap-2 bg-white text-accent text-sm font-extrabold px-5 py-2 rounded-lg hover:bg-yellow-50 transition flex-shrink-0">
                <i class="fa-solid fa-phone text-xs" aria-hidden="true"></i>
                Call <?= htmlspecialchars($site_phone) ?> Now
            </a>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════
         HERO
    ══════════════════════════════════════════════════════════ -->
    <section class="bg-dark text-white py-16 md:py-20 relative overflow-hidden">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb" class="mb-7">
                <ol class="flex items-center gap-2 text-xs text-gray-400">
                    <li><a href="<?= $base_url ?>/" class="hover:text-white transition">Home</a></li>
                    <li aria-hidden="true" class="text-gray-600">/</li>
                    <li aria-current="page" class="text-white font-medium">Contact Us</li>
                </ol>
            </nav>
            <div class="grid lg:grid-cols-2 gap-10 items-center">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-secondary mb-4">Get in Touch</p>
                    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-5 leading-tight">
                        We're Ready When<br>
                        <span class="text-secondary">You Need Us</span>
                    </h1>
                    <p class="text-gray-300 leading-relaxed mb-7 text-lg">
                        Call, email, or fill out the form. Our dispatch team responds within 2 hours during business hours — and 24/7 for emergencies across Sydney &amp; Brisbane.
                    </p>
                    <!-- Contact quick info -->
                    <div class="space-y-4">
                        <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                           class="flex items-center gap-4 bg-white/10 hover:bg-white/20 border border-white/15 rounded-2xl px-5 py-4 transition group">
                            <div class="w-11 h-11 rounded-xl bg-secondary flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition">
                                <i class="fa-solid fa-phone text-white text-lg" aria-hidden="true"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium">Call or Text — 24/7 Emergency</p>
                                <p class="text-white font-extrabold text-xl leading-tight"><?= htmlspecialchars($site_phone) ?></p>
                            </div>
                        </a>
                        <a href="mailto:<?= htmlspecialchars($site_email) ?>"
                           class="flex items-center gap-4 bg-white/10 hover:bg-white/20 border border-white/15 rounded-2xl px-5 py-4 transition group">
                            <div class="w-11 h-11 rounded-xl bg-primary/50 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition">
                                <i class="fa-solid fa-envelope text-white" aria-hidden="true"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium">Email — 1 Business Day Response</p>
                                <p class="text-white font-bold"><?= htmlspecialchars($site_email) ?></p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Hours card -->
                <div class="bg-white/10 backdrop-blur-sm border border-white/15 rounded-2xl p-7">
                    <h2 class="text-lg font-extrabold text-white mb-5 flex items-center gap-2">
                        <i class="fa-solid fa-clock text-secondary" aria-hidden="true"></i>
                        Hours of Operation
                    </h2>
                    <div class="space-y-3 mb-6">
                        <?php
                        $hours = [
                            ['Mon – Friday',         '7:00 am – 8:00 pm', false],
                            ['Saturday',             '8:00 am – 6:00 pm', false],
                            ['Sunday',               '9:00 am – 5:00 pm', false],
                            ['Emergency / 24hr Line','Always Available',   true],
                        ];
                        foreach ($hours as [$day, $time, $emerg]): ?>
                        <div class="flex items-center justify-between py-2 <?= $emerg ? 'border-t border-white/10 pt-3 mt-1' : '' ?>">
                            <span class="text-<?= $emerg ? 'secondary font-bold' : 'green-200' ?> text-sm"><?= $day ?></span>
                            <span class="text-<?= $emerg ? 'secondary font-extrabold' : 'white font-medium' ?> text-sm"><?= $time ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="<?= $base_url ?>/booking"
                       class="block text-center bg-accent hover:bg-yellow-800 text-white font-extrabold py-3.5 rounded-xl transition shadow-lg text-sm">
                        <i class="fa-solid fa-calendar-check mr-2" aria-hidden="true"></i>
                        Book Online — Free Inspection
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         CONTACT FORM + INFO
    ══════════════════════════════════════════════════════════ -->
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-10 max-w-6xl mx-auto">

                <!-- Contact Form -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="bg-primary px-8 py-5">
                        <h2 class="text-white font-extrabold text-lg">Send Us a Message</h2>
                        <p class="text-gray-400 text-xs mt-1">For non-urgent inquiries, commercial quotes, or general questions.</p>
                    </div>
                    <div class="p-8">
                        <?php if ($flash_success): ?>
                        <div class="bg-yellow-50 border border-green-200 text-green-800 text-sm px-4 py-4 rounded-xl mb-6 flex items-start gap-3">
                            <i class="fa-solid fa-circle-check text-green-600 flex-shrink-0 mt-0.5 text-lg" aria-hidden="true"></i>
                            <div>
                                <p class="font-bold mb-0.5">Message Sent!</p>
                                <p><?= htmlspecialchars($flash_success) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if ($flash_error): ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3.5 rounded-xl mb-6 flex items-start gap-3">
                            <i class="fa-solid fa-triangle-exclamation flex-shrink-0 mt-0.5" aria-hidden="true"></i>
                            <p><?= htmlspecialchars($flash_error) ?></p>
                        </div>
                        <?php endif; ?>

                        <form action="<?= $base_url ?>/process_contact" method="POST" class="space-y-5" novalidate>
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                            <div>
                                <label for="contact_name" class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">
                                    Full Name <span class="text-accent">*</span>
                                </label>
                                <input type="text" id="contact_name" name="name" required autocomplete="name"
                                       placeholder="Jane Smith"
                                       value="<?= htmlspecialchars($form_data['name'] ?? '') ?>"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition text-sm bg-gray-50 focus:bg-white">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="contact_email" class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">
                                        Email <span class="text-accent">*</span>
                                    </label>
                                    <input type="email" id="contact_email" name="email" required autocomplete="email"
                                           value="<?= htmlspecialchars($form_data['email'] ?? '') ?>"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition text-sm bg-gray-50 focus:bg-white">
                                </div>
                                <div>
                                    <label for="contact_phone" class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">
                                        Phone <span class="text-gray-400 font-normal normal-case">(optional)</span>
                                    </label>
                                    <input type="tel" id="contact_phone" name="phone" autocomplete="tel"
                                           value="<?= htmlspecialchars($form_data['phone'] ?? '') ?>"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition text-sm bg-gray-50 focus:bg-white">
                                </div>
                            </div>

                            <div>
                                <label for="contact_message" class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">
                                    Message <span class="text-accent">*</span>
                                </label>
                                <textarea id="contact_message" name="message" rows="5" required
                                          placeholder="How can we help? Tell us about the pest problem, your property, or any questions..."
                                          class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition text-sm bg-gray-50 focus:bg-white resize-none"><?= htmlspecialchars($form_data['message'] ?? '') ?></textarea>
                            </div>

                            <button type="submit"
                                    class="w-full bg-primary hover:bg-yellow-900 text-white font-extrabold text-sm py-4 rounded-xl transition flex items-center justify-center gap-2 shadow-md">
                                <i class="fa-solid fa-paper-plane text-xs" aria-hidden="true"></i>
                                Send Message
                            </button>
                            <p class="text-xs text-center text-gray-400">
                                <i class="fa-solid fa-lock mr-1 text-gray-300" aria-hidden="true"></i>
                                We never share your information. Expect a reply within 1 business day.
                            </p>
                        </form>
                    </div>
                </div>

                <!-- Info + Service Areas -->
                <div class="space-y-5">

                    <!-- Prefer to book CTA -->
                    <div class="bg-accent rounded-2xl p-7 text-white">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-calendar-check text-xl" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-lg mb-1">Book Online — It's Faster</h3>
                                <p class="text-green-100 text-sm leading-relaxed mb-4">
                                    Skip the back-and-forth. Fill out our booking form and get a call within 2 hours.
                                </p>
                                <a href="<?= $base_url ?>/booking"
                                   class="inline-flex items-center gap-2 bg-white text-accent hover:bg-yellow-50 text-sm font-extrabold px-5 py-2.5 rounded-xl transition">
                                    Book Free Inspection →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Contact cards -->
                    <?php
                    $cards = [
                        [
                            'icon'  => 'fa-map-location-dot',
                            'bg'    => 'bg-primary/10',
                            'color' => 'text-primary',
                            'title' => 'Service Areas',
                            'content' => '<p class="text-gray-500 text-sm leading-relaxed">Sydney CBD, Inner West, North Shore, Eastern Suburbs, Parramatta, Sutherland Shire, Western Sydney, Brisbane CBD, Southside Brisbane, North Brisbane, Eastern Brisbane, Logan, Ipswich &amp; surrounding areas.</p>',
                        ],
                        [
                            'icon'  => 'fa-shield-halved',
                            'bg'    => 'bg-secondary/10',
                            'color' => 'text-secondary',
                            'title' => 'Our Commitment',
                            'content' => '<p class="text-gray-500 text-sm leading-relaxed">Every treatment comes with a written service report. If pests return within your service period (30–365 days depending on plan), we come back and re-treat at <strong class="text-gray-900">zero cost</strong>.</p>',
                        ],
                    ];
                    foreach ($cards as $c): ?>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex items-start gap-5">
                        <div class="w-11 h-11 rounded-xl <?= $c['bg'] ?> <?= $c['color'] ?> flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid <?= $c['icon'] ?>" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-gray-900 mb-2"><?= $c['title'] ?></h3>
                            <?= $c['content'] ?>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <!-- Trust strip -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-4">Why Customers Choose Us</p>
                        <ul class="grid grid-cols-2 gap-3">
                            <?php
                            $trust = [
                                ['fa-certificate',  'text-primary',   'Licensed'],
                                ['fa-rotate-left',  'text-secondary', 'Free Retreatment'],
                                ['fa-bolt',         'text-accent',    '2-Hour Response'],
                                ['fa-leaf',         'text-secondary', 'Eco-Friendly'],
                                ['fa-star',         'text-yellow-500','4.9★ Rating'],
                                ['fa-dollar-sign',  'text-primary',   'Free Quotes'],
                            ];
                            foreach ($trust as [$icon, $color, $label]): ?>
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <i class="fa-solid <?= $icon ?> <?= $color ?> text-xs flex-shrink-0" aria-hidden="true"></i>
                                <?= $label ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         QUICK LINKS
    ══════════════════════════════════════════════════════════ -->
    <section class="py-10 bg-white border-t border-gray-100">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <?php
                $links = [
                    ['href' => '/services', 'icon' => 'fa-shield-bug',     'bg' => 'bg-primary/10',   'color' => 'text-primary',   'title' => 'Our Services',  'desc' => 'Bed bugs, rodents, cockroaches & more'],
                    ['href' => '/faq',      'icon' => 'fa-circle-question','bg' => 'bg-secondary/10', 'color' => 'text-secondary', 'title' => 'Browse FAQ',    'desc' => 'Common questions answered'],
                    ['href' => '/booking',  'icon' => 'fa-calendar-check', 'bg' => 'bg-accent/10',    'color' => 'text-accent',    'title' => 'Book Online',   'desc' => 'Free inspection & quote'],
                ];
                foreach ($links as $l): ?>
                <a href="<?= $base_url . $l['href'] ?>"
                   class="flex items-center gap-4 bg-gray-50 rounded-2xl border border-gray-100 p-5 hover:shadow-md transition group">
                    <div class="w-11 h-11 rounded-xl <?= $l['bg'] ?> flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid <?= $l['icon'] ?> <?= $l['color'] ?>" aria-hidden="true"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 text-sm group-hover:text-primary transition"><?= $l['title'] ?></p>
                        <p class="text-gray-400 text-xs mt-0.5"><?= $l['desc'] ?></p>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

</main>

<?php require_once BASE_DIR . '/includes/footer.php'; ?>
