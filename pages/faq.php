<?php
require_once BASE_DIR . '/includes/db.php';
require_once BASE_DIR . '/includes/seo-meta.php';

$faqs = [
    'Termite Inspections' => [
        ['icon' => 'fa-camera', 'q' => 'How often should I get a termite inspection in Sydney or Brisbane?',
         'a' => 'Australian Standard AS 3660.2 recommends annual termite inspections for all properties. In high-risk areas — particularly older homes with timber subfloors or homes near bushland — we recommend inspections every 6 months. Both NSW and QLD have high termite damage claims, and most home insurance policies do not cover termite damage.'],
        ['icon' => 'fa-house-crack', 'q' => 'Will termite treatments damage my home?',
         'a' => 'Our preferred inspection method is thermal imaging, which is completely non-invasive — no drilling, no destructive testing. Chemical barrier treatments are applied to the soil around your perimeter, not your structure. All treatments comply with Australian Standard AS 3660.1 and are applied by licensed technicians.'],
        ['icon' => 'fa-file-contract', 'q' => 'Do you provide compliant termite inspection reports?',
         'a' => 'Yes. Every inspection includes a full written report compliant with AS 3660.2, documenting all findings, risk ratings, conducive conditions, and recommendations. This report is required by most lenders and conveyancers for property transactions across NSW and QLD.'],
    ],
    'Cockroach & Spider Control' => [
        ['icon' => 'fa-bug', 'q' => 'Why are cockroaches so hard to control in Sydney and Brisbane?',
         'a' => 'Australia\'s subtropical climate provides cockroaches with year-round warmth and humidity. German cockroaches thrive in kitchens and bathrooms, while American cockroaches prefer subfloor voids and drains. Over-the-counter sprays cause colonies to scatter and hide, making the problem worse. Professional gel baiting targets the colony at its harborage zones — the only consistently effective method.'],
        ['icon' => 'fa-spider', 'q' => 'Which spiders in Sydney and Brisbane are dangerous?',
         'a' => 'The most medically significant species include the Redback Spider (found in dry sheltered areas like letterboxes and garden furniture), the Eastern Funnel-web Spider (bushland fringe suburbs), and the White-tailed Spider. We recommend professional treatment whenever Redbacks are found near play areas, sandpits, or entry points.'],
        ['icon' => 'fa-clock', 'q' => 'How long do spider and cockroach treatments last?',
         'a' => 'Interior cockroach gel bait treatments typically remain effective for 3 months. Exterior spider webbing spray treatments last 3–6 months depending on weather exposure. We recommend a combined general pest treatment every 12 months for comprehensive year-round protection.'],
    ],
    'General Questions' => [
        ['icon' => 'fa-bolt', 'q' => 'How quickly can you respond to a pest emergency in Sydney or Brisbane?',
         'a' => 'We aim to respond to all inquiries within 2 hours during business hours. For genuine pest emergencies, we offer same-day appointments across Sydney CBD, North Shore, Parramatta, Hills District, Sutherland Shire, Eastern Suburbs, Brisbane CBD, Southside Brisbane, North Brisbane, and surrounds.'],
        ['icon' => 'fa-baby', 'q' => 'Are your treatments safe for children and pets?',
         'a' => 'Safety is our top priority. All our products are APVMA-registered and applied according to label specifications. We provide clearly defined re-entry periods (typically 2–4 hours for interior sprays). For families with infants, pregnant women, or sensitive pets, we offer low-toxicity options and will always discuss these before every treatment.'],
        ['icon' => 'fa-leaf', 'q' => 'Do you offer eco-friendly pest control options?',
         'a' => 'Yes. Our Integrated Pest Management approach prioritises non-chemical methods first — exclusion, habitat modification, and physical trapping. When products are required, we select the lowest-risk, most targeted APVMA-registered options available.'],
        ['icon' => 'fa-map-location-dot', 'q' => 'What areas of Sydney and Brisbane do you service?',
         'a' => 'We service all of Greater Sydney including CBD, North Shore, Inner West, Parramatta, Hills District, Sutherland Shire, and Eastern Suburbs. In Brisbane, we cover Brisbane CBD, Southside, North Brisbane, Eastern Brisbane, Western Brisbane, Logan, and Ipswich. Contact us for areas not listed.'],
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

<main class="flex-grow bg-white">

    <!-- ── HERO ─────────────────────────────────────────────────── -->
    <section class="bg-dark text-white py-16 md:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb" class="mb-8">
                <ol class="flex items-center gap-2 text-xs text-slate-500">
                    <li><a href="<?= $base_url ?>/" class="hover:text-slate-300 transition">Home</a></li>
                    <li aria-hidden="true" class="text-slate-700">/</li>
                    <li aria-current="page" class="text-slate-300">FAQ</li>
                </ol>
            </nav>
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 bg-green-600/15 border border-green-500/20 rounded-full px-4 py-1.5 mb-5">
                    <i class="fa-solid fa-circle-question text-green-400 text-xs" aria-hidden="true"></i>
                    <span class="text-green-400 text-xs font-semibold uppercase tracking-wide">Knowledge Base</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold tracking-tight mb-5 leading-tight">
                    Common Pest Control<br>
                    <span class="text-green-400">Questions Answered</span>
                </h1>
                <p class="text-slate-300 leading-relaxed mb-6 text-lg">
                    Real answers about termite inspections, cockroach control, spider removal, treatment safety, and everything in between.
                </p>
                <!-- Category jump links -->
                <div class="flex flex-wrap gap-2">
                    <?php foreach (array_keys($faqs) as $category): ?>
                    <a href="#<?= htmlspecialchars(strtolower(preg_replace('/[^a-z0-9]+/i', '-', $category))) ?>"
                       class="text-xs font-medium bg-white/8 hover:bg-white/15 border border-white/12 text-slate-300 px-3 py-1.5 rounded-full transition">
                        <?= htmlspecialchars($category) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ── FAQ CONTENT + SIDEBAR ─────────────────────────────────── -->
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-10">

                <!-- FAQ Accordion -->
                <div class="w-full lg:w-2/3">
                    <?php
                    $cat_icons = [
                        'Termite Inspections'      => 'fa-house-crack',
                        'Cockroach & Spider Control'=> 'fa-bug',
                        'General Questions'         => 'fa-circle-question',
                        'Ant & Rodent Control'      => 'fa-cheese',
                    ];
                    foreach ($faqs as $category => $items):
                        $anchor  = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $category));
                        $cat_ico = $cat_icons[$category] ?? 'fa-circle-question';
                    ?>
                    <div id="<?= $anchor ?>" class="mb-12 scroll-mt-24">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-8 h-8 rounded-lg bg-green-600 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid <?= $cat_ico ?> text-white text-xs" aria-hidden="true"></i>
                            </div>
                            <h2 class="text-lg font-bold text-slate-900"><?= htmlspecialchars($category) ?></h2>
                            <div class="h-px flex-grow bg-slate-200"></div>
                        </div>

                        <div class="space-y-2">
                            <?php foreach ($items as $faq): ?>
                            <details class="group bg-white border border-slate-200 rounded-xl overflow-hidden">
                                <summary class="flex items-center justify-between gap-4 px-5 py-4 cursor-pointer list-none hover:bg-slate-50 transition">
                                    <div class="flex items-center gap-3">
                                        <i class="fa-solid <?= htmlspecialchars($faq['icon']) ?> text-green-600 text-sm flex-shrink-0 w-4" aria-hidden="true"></i>
                                        <span class="font-semibold text-slate-900 text-sm leading-snug"><?= htmlspecialchars($faq['q']) ?></span>
                                    </div>
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs flex-shrink-0 transition-transform group-open:rotate-180" aria-hidden="true"></i>
                                </summary>
                                <div class="px-5 pb-5 pt-1 border-t border-slate-100">
                                    <p class="text-slate-600 text-sm leading-relaxed pl-7"><?= htmlspecialchars($faq['a']) ?></p>
                                </div>
                            </details>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-7 text-center">
                        <h3 class="font-bold text-slate-900 mb-2">Still have questions?</h3>
                        <p class="text-sm text-slate-500 mb-5">Our team is ready to help. Call, email, or book a free consultation.</p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                            <a href="<?= $base_url ?>/booking"
                               class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition text-sm">
                                <i class="fa-regular fa-calendar-check" aria-hidden="true"></i>
                                Book Free Inspection
                            </a>
                            <a href="<?= $base_url ?>/contact"
                               class="inline-flex items-center gap-2 border border-slate-200 text-slate-700 hover:border-slate-300 font-medium px-6 py-3 rounded-lg transition text-sm">
                                Contact Us
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="w-full lg:w-1/3 space-y-5">

                    <!-- CTA Card -->
                    <div class="bg-dark rounded-xl border border-white/8 p-7 text-white sticky top-24">
                        <h3 class="font-bold text-white mb-2 text-sm">Ready to Book?</h3>
                        <p class="text-slate-400 text-xs leading-relaxed mb-5">
                            Get a free inspection and quote from a licensed technician today. No obligation.
                        </p>
                        <a href="<?= $base_url ?>/booking"
                           class="block text-center bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition mb-4 text-sm">
                            Book Free Inspection
                        </a>
                        <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                           class="flex items-center gap-2 text-sm text-slate-300 hover:text-white transition">
                            <i class="fa-solid fa-phone text-green-400 text-xs" aria-hidden="true"></i>
                            <?= htmlspecialchars($site_phone) ?>
                        </a>
                        <p class="text-xs text-green-400 mt-1">24/7 emergency line</p>

                        <div class="mt-6 pt-5 border-t border-white/8 space-y-3">
                            <?php
                            $trust_items = [
                                ['fa-shield-halved', 'Written service report on every job'],
                                ['fa-bolt',          '2-hour callback, business hours'],
                                ['fa-leaf',          'Eco-friendly options always available'],
                                ['fa-certificate',   'Licensed &amp; insured technicians'],
                            ];
                            foreach ($trust_items as [$icon, $label]): ?>
                            <div class="flex items-center gap-2.5 text-xs text-slate-400">
                                <i class="fa-solid <?= $icon ?> text-green-400 text-[10px] flex-shrink-0" aria-hidden="true"></i>
                                <?= $label ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

</main>

<?php require_once BASE_DIR . '/includes/footer.php'; ?>
