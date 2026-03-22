<?php
require_once BASE_DIR . '/includes/db.php';
require_once BASE_DIR . '/includes/seo-meta.php';

$page_seo = get_page_seo('thank-you', [
    'title'       => 'Booking Received — Thank You | General Pest Removal',
    'description' => 'Your pest control inspection request has been received. Our dispatch team will contact you within 2 hours.',
    'canonical'   => SITE_BASE_URL . '/thank-you',
    'noindex'     => true,
]);

require_once BASE_DIR . '/includes/header.php';
?>

<main class="flex-grow bg-gray-50">

    <!-- ═══════════════════════════════════════════════════════
         SUCCESS HERO
    ══════════════════════════════════════════════════════════ -->
    <section class="bg-dark py-16 md:py-20">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
            <!-- Animated checkmark -->
            <div class="w-20 h-20 bg-secondary rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl shadow-green-900/30">
                <i class="fa-solid fa-check text-4xl text-white" aria-hidden="true"></i>
            </div>
            <div class="inline-flex items-center gap-2 bg-secondary/20 border border-secondary/30 rounded-full px-4 py-1.5 mb-5">
                <i class="fa-solid fa-circle-check text-secondary text-xs" aria-hidden="true"></i>
                <span class="text-secondary text-xs font-semibold uppercase tracking-wide">Request Confirmed</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mb-4">
                You're All Set — We'll Be<br>in Touch Shortly!
            </h1>
            <p class="text-gray-300 text-lg leading-relaxed mb-2 max-w-xl mx-auto">
                Your inspection request has been received by our Sydney dispatch team. Expect a call within <strong class="text-white">2 hours</strong> during business hours.
            </p>
            <p class="text-green-400 text-sm">
                <i class="fa-solid fa-clock mr-1" aria-hidden="true"></i>
                Business hours: Mon–Sat 7am–8pm · Sun 9am–5pm · 24/7 Emergency Line Available
            </p>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         WHAT HAPPENS NEXT
    ══════════════════════════════════════════════════════════ -->
    <section class="py-14 md:py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-10">
                <p class="text-xs font-semibold uppercase tracking-widest text-secondary mb-3">What Happens Next</p>
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Your Next 3 Steps</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <?php
                $steps = [
                    ['icon' => 'fa-phone-volume',       'color' => 'bg-secondary', 'num' => '1', 'title' => 'We Call You',        'desc' => 'A licensed technician calls within 2 hours. They\'ll confirm your details and answer any questions about the treatment.'],
                    ['icon' => 'fa-calendar-check',     'color' => 'bg-primary',   'num' => '2', 'title' => 'Confirm Appointment', 'desc' => 'We schedule a same-day or next-day appointment that fits your schedule. Discreet, unmarked vehicles always used.'],
                    ['icon' => 'fa-house-circle-check', 'color' => 'bg-accent',    'num' => '3', 'title' => 'Pest-Free Results',    'desc' => 'We treat your property and provide a written service report. If pests return within the service period, we retreat free.'],
                ];
                foreach ($steps as $s): ?>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-7 text-center">
                    <div class="w-14 h-14 <?= $s['color'] ?> rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-md">
                        <i class="fa-solid <?= $s['icon'] ?> text-white text-xl" aria-hidden="true"></i>
                    </div>
                    <span class="text-xs font-extrabold text-gray-200 uppercase tracking-widest block mb-2">Step <?= $s['num'] ?></span>
                    <h3 class="font-extrabold text-gray-900 mb-2"><?= $s['title'] ?></h3>
                    <p class="text-sm text-gray-500 leading-relaxed"><?= $s['desc'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Emergency callout -->
            <div class="bg-yellow-50 border border-green-200 rounded-2xl p-7 mb-8">
                <div class="flex items-start gap-5">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-triangle-exclamation text-green-600 text-xl" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h2 class="font-extrabold text-gray-900 mb-2">Is This an Immediate Emergency?</h2>
                        <p class="text-sm text-gray-600 leading-relaxed mb-4">
                            If you're dealing with a severe infestation, aggressive wildlife, or a health-code-critical commercial situation — don't wait. Call our 24/7 emergency line right now.
                        </p>
                        <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                           class="inline-flex items-center gap-2 bg-primary hover:bg-yellow-900 text-white font-extrabold px-6 py-3 rounded-xl transition shadow-md text-sm">
                            <i class="fa-solid fa-phone" aria-hidden="true"></i>
                            Call <?= htmlspecialchars($site_phone) ?> Now
                        </a>
                    </div>
                </div>
            </div>

            <!-- While you wait -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                <h3 class="font-extrabold text-gray-900 text-lg mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-book-open text-primary" aria-hidden="true"></i>
                    While You Wait — Helpful Reads
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <?php
                    $reads = [
                        ['href' => '/faq',         'icon' => 'fa-circle-question', 'bg' => 'bg-primary/10',   'color' => 'text-primary',   'title' => 'Read Our FAQ',        'desc' => 'Treatment duration, safety for pets & children, what to expect.'],
                        ['href' => '/services',    'icon' => 'fa-shield-bug',      'bg' => 'bg-secondary/10', 'color' => 'text-secondary', 'title' => 'Our Services',        'desc' => 'Detailed breakdown of all our pest control methods and protection plans.'],
                        ['href' => '/blogs',       'icon' => 'fa-newspaper',       'bg' => 'bg-accent/10',    'color' => 'text-accent',    'title' => 'Pest Control Tips',   'desc' => 'Expert guides on bed bugs, rodents, cockroaches & more.'],
                    ];
                    foreach ($reads as $r): ?>
                    <a href="<?= $base_url . $r['href'] ?>"
                       class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition group">
                        <div class="w-9 h-9 rounded-lg <?= $r['bg'] ?> flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid <?= $r['icon'] ?> <?= $r['color'] ?> text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm group-hover:text-primary transition"><?= $r['title'] ?></p>
                            <p class="text-xs text-gray-400 mt-0.5 leading-snug"><?= $r['desc'] ?></p>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Trust strip -->
            <div class="mt-8 flex flex-wrap items-center justify-center gap-6 text-sm text-gray-400">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-secondary" aria-hidden="true"></i>
                    Licensed & Insured
                </span>
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-rotate-left text-secondary" aria-hidden="true"></i>
                    Free Retreatment Included
                </span>
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-star text-yellow-400" aria-hidden="true"></i>
                    4.9/5 Google Rating
                </span>
                <a href="<?= $base_url ?>/"
                   class="flex items-center gap-1.5 text-primary font-semibold hover:text-secondary transition">
                    <i class="fa-solid fa-arrow-left text-xs" aria-hidden="true"></i>
                    Back to Homepage
                </a>
            </div>

        </div>
    </section>

</main>

<?php require_once BASE_DIR . '/includes/footer.php'; ?>
