<?php
$_sc   = $GLOBALS['site_config'] ?? [];
$_name = $_sc['business_name']    ?? $site_name ?? 'General Pest Removal';

$_socials = [
    'facebook'  => ['icon' => 'fa-facebook',  'label' => 'Facebook',  'url' => $_sc['social_facebook']  ?? ''],
    'instagram' => ['icon' => 'fa-instagram', 'label' => 'Instagram', 'url' => $_sc['social_instagram'] ?? ''],
    'tiktok'    => ['icon' => 'fa-tiktok',    'label' => 'TikTok',    'url' => $_sc['social_tiktok']    ?? ''],
    'google'    => ['icon' => 'fa-google',    'label' => 'Google',    'url' => $_sc['social_google']    ?? ''],
    'yelp'      => ['icon' => 'fa-yelp',      'label' => 'Yelp',      'url' => $_sc['social_yelp']      ?? ''],
];
// Only show socials with a URL set
$_active_socials = array_filter($_socials, fn($s) => !empty($s['url']));
?>
<footer class="bg-dark text-gray-400 pt-20 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">

            <!-- Brand -->
            <div class="lg:col-span-1">
                <a href="<?= $base_url ?>/" class="flex items-center gap-2.5 mb-5" aria-label="<?= htmlspecialchars($_name) ?> Home">
                    <?php if (($_sc['logo_type'] ?? 'text') === 'image' && !empty($_sc['logo_image_url'])):
                    $footer_logo_src = preg_match('#^https?://#', $_sc['logo_image_url'])
                        ? $_sc['logo_image_url']
                        : $base_url . $_sc['logo_image_url'];
                ?>
                    <img src="<?= htmlspecialchars($footer_logo_src) ?>"
                         alt="<?= htmlspecialchars($_name) ?>"
                         class="h-9 w-auto object-contain brightness-0 invert">
                    <?php else: ?>
                    <div class="w-9 h-9 bg-primary rounded-lg flex items-center justify-center">
                        <i class="fa-solid <?= htmlspecialchars($_sc['logo_icon'] ?? 'fa-bug') ?> text-white text-sm" aria-hidden="true"></i>
                    </div>
                    <span class="text-xl font-bold text-white tracking-tight">
                        <?= htmlspecialchars($_sc['logo_text_primary'] ?? 'General') ?><span class="text-secondary"><?= htmlspecialchars($_sc['logo_text_secondary'] ?? 'Pest') ?></span>
                    </span>
                    <?php endif; ?>
                </a>
                <?php if (!empty($_sc['tagline'])): ?>
                <p class="text-sm text-gray-500 leading-relaxed mb-6">
                    <?= htmlspecialchars($_sc['tagline']) ?>
                </p>
                <?php else: ?>
                <p class="text-sm text-gray-500 leading-relaxed mb-6">
                    Australia's licensed pest control provider. Safe, effective, and proven elimination of termites, cockroaches, and rodents across Sydney &amp; Brisbane.
                </p>
                <?php endif; ?>

                <!-- Social Links -->
                <?php if (!empty($_active_socials)): ?>
                <div class="flex items-center gap-3 flex-wrap">
                    <?php foreach ($_active_socials as $s): ?>
                    <a href="<?= htmlspecialchars($s['url']) ?>"
                       target="_blank" rel="noopener noreferrer"
                       class="w-9 h-9 rounded-lg bg-white/5 hover:bg-white/15 flex items-center justify-center transition"
                       aria-label="<?= htmlspecialchars($s['label']) ?>">
                        <i class="fa-brands <?= htmlspecialchars($s['icon']) ?> text-sm" aria-hidden="true"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-lg bg-white/5 flex items-center justify-center text-gray-700">
                        <i class="fa-brands fa-facebook text-sm" aria-hidden="true"></i>
                    </span>
                    <span class="w-9 h-9 rounded-lg bg-white/5 flex items-center justify-center text-gray-700">
                        <i class="fa-brands fa-instagram text-sm" aria-hidden="true"></i>
                    </span>
                    <span class="w-9 h-9 rounded-lg bg-white/5 flex items-center justify-center text-gray-700">
                        <i class="fa-brands fa-tiktok text-sm" aria-hidden="true"></i>
                    </span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-white text-sm font-semibold uppercase tracking-widest mb-5">Navigation</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="<?= $base_url ?>/"         class="hover:text-white transition">Home</a></li>
                    <li><a href="<?= $base_url ?>/about"    class="hover:text-white transition">About Us</a></li>
                    <li><a href="<?= $base_url ?>/services" class="hover:text-white transition">Services</a></li>
                    <li><a href="<?= $base_url ?>/blogs"    class="hover:text-white transition">Blog</a></li>
                    <li><a href="<?= $base_url ?>/faq"      class="hover:text-white transition">FAQ</a></li>
                    <li><a href="<?= $base_url ?>/booking"  class="hover:text-white transition">Book Online</a></li>
                    <li><a href="<?= $base_url ?>/contact"  class="hover:text-white transition">Contact Us</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div>
                <h4 class="text-white text-sm font-semibold uppercase tracking-widest mb-5">Services</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="<?= $base_url ?>/services#termites" class="hover:text-white transition">Termite Inspections</a></li>
                    <li><a href="<?= $base_url ?>/services#rodents"     class="hover:text-white transition">Mice &amp; Rat Removal</a></li>
                    <li><a href="<?= $base_url ?>/services#cockroaches" class="hover:text-white transition">Cockroach Control</a></li>
                    <li><a href="<?= $base_url ?>/booking/bedbugs"      class="hover:text-white transition">Book Bed Bug Inspection</a></li>
                    <li><a href="<?= $base_url ?>/booking/rodents"      class="hover:text-white transition">Book Rodent Control</a></li>
                    <li><a href="<?= $base_url ?>/booking"              class="hover:text-white transition">Get a Free Quote</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 class="text-white text-sm font-semibold uppercase tracking-widest mb-5">Contact</h4>
                <ul class="space-y-4 text-sm">
                    <?php if (!empty($_sc['address'])): ?>
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-location-dot mt-0.5 text-secondary flex-shrink-0" aria-hidden="true"></i>
                        <span><?= htmlspecialchars($_sc['address']) ?></span>
                    </li>
                    <?php endif; ?>
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-phone mt-0.5 text-secondary flex-shrink-0" aria-hidden="true"></i>
                        <div class="space-y-1">
                            <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>" class="hover:text-white transition block">
                                <?= htmlspecialchars($site_phone) ?>
                            </a>
                            <?php if (!empty($_sc['phone_secondary']) && !empty($_sc['phone_secondary_raw'])): ?>
                            <a href="tel:<?= htmlspecialchars($_sc['phone_secondary_raw']) ?>" class="hover:text-white transition block">
                                <?= htmlspecialchars($_sc['phone_secondary']) ?>
                            </a>
                            <?php endif; ?>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-envelope mt-0.5 text-secondary flex-shrink-0" aria-hidden="true"></i>
                        <a href="mailto:<?= htmlspecialchars($site_email) ?>" class="hover:text-white transition">
                            <?= htmlspecialchars($site_email) ?>
                        </a>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-clock mt-0.5 text-secondary flex-shrink-0" aria-hidden="true"></i>
                        <div class="space-y-0.5">
                            <?php if (!empty($_sc['hours_weekday'])): ?>
                            <p><?= htmlspecialchars($_sc['hours_weekday']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($_sc['hours_emergency'])): ?>
                            <p class="text-secondary font-medium"><?= htmlspecialchars($_sc['hours_emergency']) ?></p>
                            <?php endif; ?>
                        </div>
                    </li>
                </ul>
                <div class="mt-6">
                    <a href="<?= $base_url ?>/booking"
                       class="inline-block bg-accent hover:bg-yellow-800 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
                        Book an Inspection
                    </a>
                </div>
            </div>

        </div>

        <!-- Service Areas -->
        <div class="border-t border-white/5 pt-8 mb-6">
            <p class="text-xs text-gray-600 text-center leading-relaxed">
                <span class="text-gray-500 font-medium">Service Areas:</span>
                <?php
                $areas = ['Sydney CBD','Inner West','North Shore','Eastern Suburbs','Western Sydney','Parramatta','Hills District','Sutherland Shire','Brisbane CBD','Southside Brisbane','North Brisbane','Eastern Brisbane','Western Brisbane','Logan','Ipswich'];
                $links = array_map(fn($a) => '<a href="' . $base_url . '/booking" class="hover:text-gray-300 transition">' . $a . '</a>', $areas);
                echo implode(' <span class="text-gray-700 mx-1">&middot;</span> ', $links);
                ?>
            </p>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-white/5 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-gray-600">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($_name) ?>. All rights reserved. Licensed &amp; Insured.</p>
            <div class="flex items-center gap-5">
                <a href="<?= $base_url ?>/sitemap.xml" class="hover:text-gray-300 transition">Sitemap</a>
                <a href="#" class="hover:text-gray-300 transition">Privacy Policy</a>
                <a href="#" class="hover:text-gray-300 transition">Terms of Service</a>
                <a href="<?= $base_url ?>/admin" class="hover:text-gray-300 transition">Admin</a>
            </div>
        </div>
    </div>
</footer>
</body>
</html>
