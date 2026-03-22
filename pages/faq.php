<?php
require_once BASE_DIR . '/includes/db.php';
require_once BASE_DIR . '/includes/seo-meta.php';

$faqs = [
    'Termite Inspections' => [
        ['icon' => 'fa-camera', 'q' => 'How often should I get a termite inspection in Sydney or Brisbane?',
         'a' => 'Australian Standard AS 3660.2 recommends annual termite inspections for all properties. In high-risk areas — particularly older homes with timber subfloors or homes near bushland in Sydney and Brisbane — we recommend inspections every 6 months. Both NSW and QLD have high termite damage claims, and most home insurance policies do not cover termite damage.'],
        ['icon' => 'fa-house-crack', 'q' => 'Will termite treatments damage my home?',
         'a' => 'Our preferred inspection method is thermal imaging, which is completely non-invasive — no drilling, no destructive testing. Chemical barrier treatments are applied to the soil around your perimeter, not your structure. All our treatments comply with Australian Standard AS 3660.1 and are applied by licensed technicians.'],
        ['icon' => 'fa-file-contract', 'q' => 'Do you provide compliant termite inspection reports?',
         'a' => 'Yes. Every inspection includes a full written report compliant with AS 3660.2, documenting all findings, risk ratings, conducive conditions, and recommendations. This report is required by most lenders and conveyancers for property transactions across NSW and QLD and is essential for insurance documentation.'],
    ],
    'Cockroach & Spider Control' => [
        ['icon' => 'fa-bug', 'q' => 'Why are cockroaches so hard to control in Sydney and Brisbane?',
         'a' => 'Australia\'s subtropical climate provides cockroaches with year-round warmth and humidity across both Sydney and Brisbane. German cockroaches thrive in kitchens and bathrooms, while American cockroaches prefer subfloor voids and drains. Over-the-counter sprays cause colonies to scatter and hide, making the problem worse. Professional gel baiting targets the colony at its harborage zones — the only consistently effective method.'],
        ['icon' => 'fa-spider', 'q' => 'Which spiders in Sydney and Brisbane are dangerous?',
         'a' => 'The most medically significant species in Sydney and Brisbane include the Redback Spider (found in dry sheltered areas like letterboxes and garden furniture), the Eastern Funnel-web Spider (bushland fringe suburbs), and the White-tailed Spider. We recommend professional treatment whenever Redbacks are found near play areas, sandpits, or entry points.'],
        ['icon' => 'fa-clock', 'q' => 'How long do spider and cockroach treatments last?',
         'a' => 'Interior cockroach gel bait treatments typically remain effective for 3 months. Exterior spider webbing spray treatments last 3–6 months depending on weather exposure. We recommend a combined general pest treatment (cockroaches, spiders, ants, silverfish) every 12 months for comprehensive year-round protection.'],
    ],
    'General Questions' => [
        ['icon' => 'fa-bolt', 'q' => 'How quickly can you respond to a pest emergency in Sydney or Brisbane?',
         'a' => 'We aim to respond to all inquiries within 2 hours during business hours. For genuine pest emergencies, we offer same-day appointments across Sydney CBD, North Shore, Parramatta, Hills District, Sutherland Shire, Eastern Suburbs, Brisbane CBD, Southside Brisbane, North Brisbane, and surrounds. After-hours emergency call-outs are available for severe infestations.'],
        ['icon' => 'fa-baby', 'q' => 'Are your treatments safe for children and pets?',
         'a' => 'Safety is our top priority. All our products are APVMA-registered and applied according to label specifications. We provide clearly defined re-entry periods (typically 2–4 hours for interior sprays). For families with infants, pregnant women, or sensitive pets, we offer low-toxicity options and will always discuss these before every treatment.'],
        ['icon' => 'fa-leaf', 'q' => 'Do you offer eco-friendly pest control options?',
         'a' => 'Yes. Our Integrated Pest Management approach prioritises non-chemical methods first — exclusion, habitat modification, and physical trapping. For termites, we offer reticulation systems with reduced-chemical-application profiles. When products are required, we select the lowest-risk, most targeted APVMA-registered options available.'],
        ['icon' => 'fa-map-location-dot', 'q' => 'What areas of Sydney and Brisbane do you service?',
         'a' => 'We service all of Greater Sydney including Sydney CBD, North Shore, Inner West, Parramatta, Hills District, Sutherland Shire, and the Eastern Suburbs. In Brisbane, we cover Brisbane CBD, Southside (Sunnybank, Carindale, Eight Mile Plains), North Brisbane (Chermside, Aspley, Albany Creek), Eastern Brisbane, Western Brisbane, Logan, and Ipswich. Contact us for areas not listed — we can often accommodate.'],
        ['icon' => 'fa-key', 'q' => 'Do I need to be home during the treatment?',
         'a' => 'For most interior treatments, an adult must be present to provide access and sign the service agreement. For exterior-only services (perimeter spray, webbing treatment, termite barriers), we can arrange keyless access by prior arrangement. We provide a full pre-treatment checklist so you know exactly what to expect and how long to vacate.'],
    ],
    'Ant & Rodent Control' => [
        ['icon' => 'fa-circle-dot', 'q' => 'What should I do if I think I have Fire Ants?',
         'a' => 'Do not disturb the nest. Fire Ants are a declared biosecurity matter in both NSW and QLD — disturbance causes them to scatter and establish new nests. Report suspected Fire Ants to Biosecurity NSW (13 25 23) or Biosecurity Queensland (13 25 23). As licensed pest operators, we assist with species identification, professional treatment, and mandatory reporting documentation.'],
        ['icon' => 'fa-cheese', 'q' => 'How do I stop rats and mice from coming back?',
         'a' => 'Rats and mice return because entry points are never sealed. Baiting and trapping only reduces the population inside — it does not stop new rodents entering from outside. Our exclusion service identifies and permanently seals every gap, crack, pipe penetration, and weep hole with rodent-proof materials, breaking the cycle of re-infestation.'],
    ],
];

$faq_schema_entities = [];
foreach ($faqs as $items) {
    foreach ($items as $item) {
        $faq_schema_entities[] = ['@type' => 'Question', 'name' => $item['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['a']]];
    }
}

$page_seo = get_page_seo('faq', [
    'title'       => 'Pest Removal FAQ — Sydney & Brisbane | Common Questions Answered',
    'description' => 'Expert answers to common pest questions for Sydney and Brisbane homeowners. Termites, cockroaches, spiders, ants, treatment safety, and service areas.',
    'canonical'   => SITE_BASE_URL . '/faq',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => '/'],
        ['name' => 'FAQ',  'url' => '/faq'],
    ],
    'schema' => ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $faq_schema_entities],
]);

require_once BASE_DIR . '/includes/header.php';
?>

<main class="flex-grow bg-gray-50">

    <!-- ═══════════════════════════════════════════════════════
         HERO
    ══════════════════════════════════════════════════════════ -->
    <section class="bg-dark text-white py-16 md:py-24 relative overflow-hidden">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb" class="mb-8">
                <ol class="flex items-center gap-2 text-xs text-gray-400">
                    <li><a href="<?= $base_url ?>/" class="hover:text-white transition">Home</a></li>
                    <li aria-hidden="true" class="text-gray-600">/</li>
                    <li aria-current="page" class="text-white font-medium">FAQ</li>
                </ol>
            </nav>
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 bg-secondary/20 border border-secondary/30 rounded-full px-4 py-1.5 mb-5">
                        <i class="fa-solid fa-circle-question text-secondary text-xs" aria-hidden="true"></i>
                        <span class="text-secondary text-xs font-semibold uppercase tracking-wide">Knowledge Base</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight mb-5 leading-tight">
                        Common Pest Control<br>
                        <span class="text-secondary">Questions Answered</span>
                    </h1>
                    <p class="text-gray-300 leading-relaxed mb-6 text-lg">
                        Real answers about termite inspections, cockroach control, spider removal, treatment safety, and everything in between.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach (array_keys($faqs) as $category): ?>
                        <a href="#<?= htmlspecialchars(strtolower(preg_replace('/[^a-z0-9]+/i', '-', $category))) ?>"
                           class="text-xs font-medium bg-white/10 hover:bg-white/20 border border-white/15 text-white px-3 py-1.5 rounded-full transition">
                            <?= htmlspecialchars($category) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- Trust callout -->
                <div class="grid grid-cols-2 gap-4">
                    <?php
                    $faq_trust = [
                        ['fa-shield-halved', 'text-secondary', 'Service Reports',     'On every job'],
                        ['fa-bolt',          'text-yellow-400','2-Hour Response',      'Business hours'],
                        ['fa-leaf',          'text-secondary', 'Eco-Friendly Options', 'Always available'],
                        ['fa-certificate',   'text-secondary', 'Licensed',        'Every tech certified'],
                    ];
                    foreach ($faq_trust as [$icon, $color, $title, $sub]): ?>
                    <div class="bg-white/10 border border-white/10 rounded-xl p-4 backdrop-blur-sm">
                        <i class="fa-solid <?= $icon ?> <?= $color ?> text-xl mb-2 block" aria-hidden="true"></i>
                        <p class="text-white font-bold text-sm leading-tight"><?= $title ?></p>
                        <p class="text-gray-400 text-xs mt-0.5"><?= $sub ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         FAQ CONTENT + SIDEBAR
    ══════════════════════════════════════════════════════════ -->
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-10">

                <!-- ── FAQ Accordion ─────────────────────────── -->
                <div class="w-full lg:w-2/3">
                    <?php
                    $cat_icons = [
                        'Bed Bug Treatments' => 'fa-fire',
                        'Rodent Control'     => 'fa-cheese',
                        'General Questions'  => 'fa-circle-question',
                        'Cockroach Control'  => 'fa-bug',
                    ];
                    foreach ($faqs as $category => $items):
                        $anchor  = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $category));
                        $cat_ico = $cat_icons[$category] ?? 'fa-circle-question';
                    ?>
                    <div id="<?= $anchor ?>" class="mb-12 scroll-mt-24">
                        <!-- Category header -->
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid <?= $cat_ico ?> text-white text-sm" aria-hidden="true"></i>
                            </div>
                            <h2 class="text-xl font-extrabold text-gray-900"><?= htmlspecialchars($category) ?></h2>
                            <div class="h-px flex-grow bg-gray-200"></div>
                        </div>

                        <div class="space-y-3">
                            <?php foreach ($items as $i => $item): ?>
                            <details class="bg-white rounded-2xl border border-gray-100 shadow-sm group" <?= $i === 0 && $category === 'Bed Bug Treatments' ? 'open' : '' ?>>
                                <summary class="flex items-start gap-4 px-6 py-5 cursor-pointer list-none group-open:border-b group-open:border-gray-100">
                                    <div class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0 mt-0.5 group-open:bg-primary transition">
                                        <i class="fa-solid <?= $item['icon'] ?> text-primary group-open:text-white text-xs transition" aria-hidden="true"></i>
                                    </div>
                                    <span class="font-semibold text-gray-900 group-open:text-primary transition text-sm flex-grow pr-4 leading-snug mt-0.5"><?= htmlspecialchars($item['q']) ?></span>
                                    <i class="fa-solid fa-chevron-down text-gray-400 group-open:rotate-180 transition-transform duration-200 flex-shrink-0 mt-1 text-xs" aria-hidden="true"></i>
                                </summary>
                                <div class="px-6 pb-6 pt-4 text-gray-500 text-sm leading-relaxed">
                                    <?= $item['a'] ?>
                                </div>
                            </details>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <!-- Bottom CTA -->
                    <div class="bg-primary rounded-2xl p-10 text-white text-center">
                        <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-headset text-2xl" aria-hidden="true"></i>
                        </div>
                        <h2 class="text-2xl font-extrabold tracking-tight mb-3">Still Have a Question?</h2>
                        <p class="text-gray-300 text-sm leading-relaxed mb-7 max-w-lg mx-auto">
                            Our licensed team is happy to answer anything before you book. Call us or submit a free inspection request — no obligation.
                        </p>
                        <div class="flex flex-wrap justify-center gap-3">
                            <a href="<?= $base_url ?>/booking"
                               class="inline-flex items-center gap-2 bg-accent hover:bg-yellow-800 text-white text-sm font-extrabold px-6 py-3 rounded-xl transition shadow-lg">
                                <i class="fa-solid fa-calendar-check" aria-hidden="true"></i>
                                Book Free Inspection
                            </a>
                            <a href="tel:<?= $site_phone_raw ?>"
                               class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/10 text-white text-sm font-semibold px-6 py-3 rounded-xl transition">
                                <i class="fa-solid fa-phone text-xs" aria-hidden="true"></i>
                                <?= $site_phone ?>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ── Sticky Sidebar ────────────────────────── -->
                <aside class="w-full lg:w-1/3 space-y-5">
                    <div class="lg:sticky lg:top-24">

                        <!-- CTA widget -->
                        <div class="bg-accent rounded-2xl p-7 text-white text-center mb-5">
                            <div class="w-14 h-14 bg-white/15 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-calendar-check text-2xl" aria-hidden="true"></i>
                            </div>
                            <h3 class="font-extrabold text-lg mb-2">Book a FREE Inspection</h3>
                            <p class="text-green-100 text-xs leading-relaxed mb-5">
                                We call back within 2 hours. No obligation. No credit card needed.
                            </p>
                            <a href="<?= $base_url ?>/booking"
                               class="block bg-white text-accent text-sm font-extrabold py-3.5 rounded-xl hover:bg-yellow-50 transition shadow-md">
                                Get Free Quote Now →
                            </a>
                            <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                               class="flex items-center justify-center gap-2 mt-3 text-green-200 hover:text-white text-sm font-semibold transition">
                                <i class="fa-solid fa-phone text-xs" aria-hidden="true"></i>
                                <?= htmlspecialchars($site_phone) ?>
                            </a>
                        </div>

                        <!-- Jump nav -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-4">Jump to Section</p>
                            <nav class="space-y-1" aria-label="FAQ navigation">
                                <?php foreach (array_keys($faqs) as $category):
                                    $anchor  = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $category));
                                    $cat_ico = $cat_icons[$category] ?? 'fa-circle-question';
                                ?>
                                <a href="#<?= $anchor ?>"
                                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-primary hover:text-white font-medium transition group">
                                    <i class="fa-solid <?= $cat_ico ?> text-gray-400 group-hover:text-white text-xs w-4 text-center" aria-hidden="true"></i>
                                    <?= htmlspecialchars($category) ?>
                                    <i class="fa-solid fa-arrow-right text-xs ml-auto opacity-0 group-hover:opacity-100 transition" aria-hidden="true"></i>
                                </a>
                                <?php endforeach; ?>
                            </nav>
                        </div>

                        <!-- Quick trust -->
                        <div class="bg-gray-50 rounded-2xl border border-gray-100 p-6">
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-4">Why Customers Trust Us</p>
                            <ul class="space-y-3">
                                <?php
                                $trust_items = [
                                    ['fa-id-card-clip',  'text-primary',   'Licensed Technicians'],
                                    ['fa-rotate-left',   'text-secondary', 'Free Retreatment Included'],
                                    ['fa-leaf',          'text-secondary', 'Eco-Friendly Options Always'],
                                    ['fa-star',          'text-yellow-500','4.9/5 Google Rating'],
                                    ['fa-bolt',          'text-accent',    '2-Hour Response Commitment'],
                                ];
                                foreach ($trust_items as [$icon, $color, $label]): ?>
                                <li class="flex items-center gap-3 text-sm text-gray-700">
                                    <i class="fa-solid <?= $icon ?> <?= $color ?> w-4 flex-shrink-0" aria-hidden="true"></i>
                                    <?= $label ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                    </div>
                </aside>

            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         RELATED RESOURCES
    ══════════════════════════════════════════════════════════ -->
    <section class="py-12 bg-white border-t border-gray-100">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <?php
                $resources = [
                    ['href' => '/services', 'icon' => 'fa-shield-bug',       'color' => 'text-primary',   'bg' => 'bg-primary/10',   'title' => 'Our Services',     'desc' => 'Full breakdown of treatments, methods, and pricing plans.'],
                    ['href' => '/blogs',    'icon' => 'fa-newspaper',         'color' => 'text-secondary', 'bg' => 'bg-secondary/10', 'title' => 'Pest Control Blog', 'desc' => 'Expert guides to identifying and preventing pest problems in Sydney & Brisbane.'],
                    ['href' => '/about',    'icon' => 'fa-certificate',       'color' => 'text-accent',    'bg' => 'bg-accent/10',    'title' => 'About Our Team',   'desc' => 'NSW certifications, eco approach, and company background.'],
                ];
                foreach ($resources as $r): ?>
                <a href="<?= $base_url . $r['href'] ?>"
                   class="flex items-start gap-4 bg-gray-50 rounded-2xl border border-gray-100 p-6 hover:shadow-md transition group">
                    <div class="w-11 h-11 rounded-xl <?= $r['bg'] ?> flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid <?= $r['icon'] ?> <?= $r['color'] ?>" aria-hidden="true"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 text-sm mb-1 group-hover:text-primary transition"><?= $r['title'] ?></p>
                        <p class="text-gray-400 text-xs leading-relaxed"><?= $r['desc'] ?></p>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

</main>

<?php require_once BASE_DIR . '/includes/footer.php'; ?>
