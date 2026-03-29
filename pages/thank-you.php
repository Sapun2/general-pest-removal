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
<?php
// ── Google Ads Conversion Event ──────────────────────────────────────────────
// Fires on every successful form submission (booking + contact) that lands here.
$_gads_id    = $_sc['gads_id']    ?? '';
$_gads_label = $_sc['gads_label'] ?? '';
if (!empty($_gads_id) && !empty($_gads_label)):
?>
<script>
    (function () {
        if (typeof gtag !== 'function') return;
        gtag('event', 'conversion', {
            'send_to': '<?= htmlspecialchars($_gads_id, ENT_QUOTES) ?>/<?= htmlspecialchars($_gads_label, ENT_QUOTES) ?>'
        });
    })();
</script>
<?php endif; ?>

<main class="flex-grow bg-slate-50">

    <!-- ── SUCCESS HERO ──────────────────────────────────────── -->
    <section class="bg-dark py-16 md:py-20">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 text-center">
            <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-green-900/30">
                <i class="fa-solid fa-check text-white text-2xl" aria-hidden="true"></i>
            </div>
            <div class="inline-flex items-center gap-2 bg-green-600/15 border border-green-500/20 rounded-full px-4 py-1.5 mb-5">
                <i class="fa-solid fa-circle-check text-green-400 text-xs" aria-hidden="true"></i>
                <span class="text-green-400 text-xs font-semibold uppercase tracking-wide">Request Confirmed</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-bold text-white tracking-tight mb-4 leading-tight">
                You're All Set —<br>
                <span class="text-green-400">We'll Be in Touch Shortly</span>
            </h1>
            <p class="text-slate-300 text-lg leading-relaxed mb-3 max-w-lg mx-auto">
                Your inspection request has been received. Expect a call within <strong class="text-white font-semibold">2 hours</strong> during business hours.
            </p>
            <p class="text-green-400 text-sm">
                <i class="fa-solid fa-clock mr-1.5" aria-hidden="true"></i>
                Mon–Fri 7am–8pm · Sat 8am–6pm · Sun 9am–5pm · 24/7 Emergency Line
            </p>
        </div>
    </section>

    <!-- ── WHAT HAPPENS NEXT ──────────────────────────────────── -->
    <section class="py-16 md:py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-10">
                <p class="text-xs font-semibold uppercase tracking-widest text-green-600 mb-3">Next Steps</p>
                <h2 class="text-2xl md:text-3xl font-bold text-slate-900 tracking-tight">What Happens Now</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-10">
                <?php
                $steps = [
                    ['icon' => 'fa-phone-volume',       'num' => '1', 'title' => 'We Call You',          'desc' => 'A licensed technician calls within 2 hours to confirm your details and answer any questions about the treatment.'],
                    ['icon' => 'fa-calendar-check',     'num' => '2', 'title' => 'Confirm Appointment',  'desc' => 'We schedule a same-day or next-day time that works for you. Discreet, unmarked vehicles always used.'],
                    ['icon' => 'fa-house-circle-check', 'num' => '3', 'title' => 'Pest-Free Results',    'desc' => 'We treat your property and issue a written service report. If pests return within the guarantee period, we retreat free.'],
                ];
                foreach ($steps as $s): ?>
                <div class="bg-white rounded-xl border border-slate-200 p-7 text-center">
                    <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid <?= $s['icon'] ?> text-white text-lg" aria-hidden="true"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-300 uppercase tracking-widest block mb-2">Step <?= $s['num'] ?></span>
                    <h3 class="font-bold text-slate-900 mb-2 text-sm"><?= $s['title'] ?></h3>
                    <p class="text-sm text-slate-500 leading-relaxed"><?= $s['desc'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Emergency callout -->
            <div class="bg-white border border-slate-200 rounded-xl p-7 mb-6">
                <div class="flex items-start gap-5">
                    <div class="w-11 h-11 bg-green-50 border border-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-bolt text-green-600 text-base" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-900 mb-2">Is This an Immediate Emergency?</h2>
                        <p class="text-sm text-slate-500 leading-relaxed mb-4">
                            Severe infestation, aggressive wildlife, or a health-code-critical situation? Don't wait — call our 24/7 emergency line right now.
                        </p>
                        <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition text-sm">
                            <i class="fa-solid fa-phone text-xs" aria-hidden="true"></i>
                            Call <?= htmlspecialchars($site_phone) ?> Now
                        </a>
                    </div>
                </div>
            </div>

            <!-- While you wait -->
            <div class="bg-white rounded-xl border border-slate-200 p-7 mb-8">
                <h3 class="font-bold text-slate-900 text-sm mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-book-open text-green-600 text-sm" aria-hidden="true"></i>
                    While You Wait — Helpful Reads
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <?php
                    $reads = [
                        ['href' => '/faq',      'icon' => 'fa-circle-question', 'title' => 'Read Our FAQ',      'desc' => 'Treatment duration, pet safety, what to expect on the day.'],
                        ['href' => '/services', 'icon' => 'fa-shield-bug',      'title' => 'Our Services',      'desc' => 'Detailed breakdown of all pest control methods and plans.'],
                        ['href' => '/blogs',    'icon' => 'fa-newspaper',       'title' => 'Pest Control Tips', 'desc' => 'Expert guides on termites, rodents, cockroaches and more.'],
                    ];
                    foreach ($reads as $r): ?>
                    <a href="<?= $base_url . $r['href'] ?>"
                       class="flex items-start gap-3 p-4 bg-slate-50 rounded-lg hover:bg-slate-100 transition group">
                        <div class="w-9 h-9 rounded-lg bg-green-50 border border-green-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid <?= $r['icon'] ?> text-green-600 text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900 text-sm group-hover:text-green-700 transition"><?= $r['title'] ?></p>
                            <p class="text-xs text-slate-400 mt-0.5 leading-snug"><?= $r['desc'] ?></p>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Trust strip -->
            <div class="flex flex-wrap items-center justify-center gap-6 text-sm text-slate-400">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-green-600 text-xs" aria-hidden="true"></i>
                    Licensed &amp; Insured
                </span>
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-rotate-left text-green-600 text-xs" aria-hidden="true"></i>
                    Free Retreatment Guarantee
                </span>
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-star text-green-600 text-xs" aria-hidden="true"></i>
                    4.9/5 Google Rating
                </span>
                <a href="<?= $base_url ?>/"
                   class="flex items-center gap-1.5 text-green-600 font-semibold hover:text-green-700 transition text-sm">
                    <i class="fa-solid fa-arrow-left text-xs" aria-hidden="true"></i>
                    Back to Homepage
                </a>
            </div>

        </div>
    </section>

</main>

<?php require_once BASE_DIR . '/includes/footer.php'; ?>
