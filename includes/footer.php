<?php
$_sc   = $GLOBALS['site_config'] ?? [];
$_name = $_sc['business_name'] ?? $site_name ?? 'General Pest Removal';

$_socials = [
    'facebook'  => ['icon' => 'fa-facebook',  'label' => 'Facebook',  'url' => $_sc['social_facebook']  ?? ''],
    'instagram' => ['icon' => 'fa-instagram', 'label' => 'Instagram', 'url' => $_sc['social_instagram'] ?? ''],
    'tiktok'    => ['icon' => 'fa-tiktok',    'label' => 'TikTok',    'url' => $_sc['social_tiktok']    ?? ''],
    'google'    => ['icon' => 'fa-google',    'label' => 'Google',    'url' => $_sc['social_google']    ?? ''],
];
$_active_socials = array_filter($_socials, fn($s) => !empty($s['url']));
?>
<footer class="bg-dark text-slate-400">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-8">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">

            <!-- Brand -->
            <div>
                <a href="<?= $base_url ?>/" class="inline-flex items-center gap-2.5 mb-4" aria-label="<?= htmlspecialchars($_name) ?> Home">
                    <?php if (($_sc['logo_type'] ?? 'text') === 'image' && !empty($_sc['logo_image_url'])):
                        $footer_logo_src = preg_match('#^https?://#', $_sc['logo_image_url'])
                            ? $_sc['logo_image_url']
                            : $base_url . $_sc['logo_image_url'];
                    ?>
                    <img src="<?= htmlspecialchars($footer_logo_src) ?>"
                         alt="<?= htmlspecialchars($_name) ?>"
                         class="h-8 w-auto object-contain brightness-0 invert">
                    <?php else: ?>
                    <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid <?= htmlspecialchars($_sc['logo_icon'] ?? 'fa-bug') ?> text-white text-xs" aria-hidden="true"></i>
                    </div>
                    <span class="text-lg font-bold text-white tracking-tight">
                        <?= htmlspecialchars($_sc['logo_text_primary'] ?? 'General') ?><span class="text-green-500"><?= htmlspecialchars($_sc['logo_text_secondary'] ?? 'Pest') ?></span>
                    </span>
                    <?php endif; ?>
                </a>

                <p class="text-sm text-slate-500 leading-relaxed mb-5">
                    <?= htmlspecialchars($_sc['tagline'] ?? 'Licensed pest control across Sydney & Brisbane. Safe, proven elimination of termites, cockroaches, rodents & more.') ?>
                </p>

                <?php if (!empty($_active_socials)): ?>
                <div class="flex items-center gap-2">
                    <?php foreach ($_active_socials as $s): ?>
                    <a href="<?= htmlspecialchars($s['url']) ?>"
                       target="_blank" rel="noopener noreferrer"
                       class="w-8 h-8 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center transition"
                       aria-label="<?= htmlspecialchars($s['label']) ?>">
                        <i class="fa-brands <?= htmlspecialchars($s['icon']) ?> text-sm" aria-hidden="true"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Navigation -->
            <div>
                <h4 class="text-white text-xs font-semibold uppercase tracking-widest mb-4">Navigation</h4>
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
                <h4 class="text-white text-xs font-semibold uppercase tracking-widest mb-4">Services</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="<?= $base_url ?>/services#termites"    class="hover:text-white transition">Termite Inspections</a></li>
                    <li><a href="<?= $base_url ?>/services#rodents"     class="hover:text-white transition">Mice &amp; Rat Removal</a></li>
                    <li><a href="<?= $base_url ?>/services#cockroaches" class="hover:text-white transition">Cockroach Control</a></li>
                    <li><a href="<?= $base_url ?>/services#spiders"     class="hover:text-white transition">Spider Removal</a></li>
                    <li><a href="<?= $base_url ?>/services#ants"        class="hover:text-white transition">Ant Treatment</a></li>
                    <li><a href="<?= $base_url ?>/booking"              class="hover:text-white transition">Get a Free Quote</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 class="text-white text-xs font-semibold uppercase tracking-widest mb-4">Contact</h4>
                <ul class="space-y-4 text-sm">
                    <?php if (!empty($_sc['address'])): ?>
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-location-dot mt-0.5 text-green-500 flex-shrink-0 text-xs" aria-hidden="true"></i>
                        <span><?= htmlspecialchars($_sc['address']) ?></span>
                    </li>
                    <?php endif; ?>
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-phone mt-0.5 text-green-500 flex-shrink-0 text-xs" aria-hidden="true"></i>
                        <div class="space-y-1">
                            <a href="tel:<?= htmlspecialchars($site_phone_raw) ?>"
                               class="hover:text-white transition block">
                                <?= htmlspecialchars($site_phone) ?>
                            </a>
                            <?php if (!empty($_sc['phone_secondary']) && !empty($_sc['phone_secondary_raw'])): ?>
                            <a href="tel:<?= htmlspecialchars($_sc['phone_secondary_raw']) ?>"
                               class="hover:text-white transition block">
                                <?= htmlspecialchars($_sc['phone_secondary']) ?>
                            </a>
                            <?php endif; ?>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-envelope mt-0.5 text-green-500 flex-shrink-0 text-xs" aria-hidden="true"></i>
                        <a href="mailto:<?= htmlspecialchars($site_email) ?>"
                           class="hover:text-white transition break-all">
                            <?= htmlspecialchars($site_email) ?>
                        </a>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-clock mt-0.5 text-green-500 flex-shrink-0 text-xs" aria-hidden="true"></i>
                        <div class="space-y-0.5 text-slate-500">
                            <?php if (!empty($_sc['hours_weekday'])): ?>
                            <p><?= htmlspecialchars($_sc['hours_weekday']) ?></p>
                            <?php else: ?>
                            <p>Mon–Sat 7am–8pm · Sun 9am–5pm</p>
                            <?php endif; ?>
                            <p class="text-green-500 font-medium">24/7 Emergency Line</p>
                        </div>
                    </li>
                </ul>
                <div class="mt-6">
                    <a href="<?= $base_url ?>/booking"
                       class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
                        Book an Inspection
                    </a>
                </div>
            </div>

        </div>

        <!-- Service Areas -->
        <div class="border-t border-white/5 pt-6 mb-6">
            <p class="text-xs text-slate-600 text-center leading-relaxed">
                <span class="text-slate-500 font-medium">Service Areas: </span>
                <?php
                $areas = ['Sydney CBD','Inner West','North Shore','Eastern Suburbs','Western Sydney','Parramatta','Hills District','Sutherland Shire','Brisbane CBD','Southside Brisbane','North Brisbane','Eastern Brisbane','Logan','Ipswich'];
                $links = array_map(fn($a) => '<a href="' . $base_url . '/booking" class="hover:text-slate-300 transition">' . $a . '</a>', $areas);
                echo implode(' <span class="text-slate-700 mx-1">·</span> ', $links);
                ?>
            </p>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-white/5 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-slate-600">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($_name) ?>. All rights reserved. Licensed &amp; Insured.</p>
            <div class="flex items-center gap-5 flex-wrap justify-center">
                <a href="<?= $base_url ?>/sitemap.xml"  class="hover:text-slate-300 transition">Sitemap</a>
                <a href="#"                              class="hover:text-slate-300 transition">Privacy Policy</a>
                <a href="#"                              class="hover:text-slate-300 transition">Terms</a>
                <a href="<?= $base_url ?>/admin"         class="hover:text-slate-300 transition">Admin</a>
            </div>
        </div>

    </div>
</footer>

<!-- General Pest Removal CRM Integration -->
<script>
(function() {
  var API_KEY = 'crm_af1b0fe74ce8f8a388d8eeba6934707a3f610532';
  var API_URL = 'https://nepalitechsupport.tech';

  // Submit a booking
  window.crmBooking = function(data, callback) {
    fetch(API_URL + '/api/booking', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-API-Key': API_KEY },
      body: JSON.stringify(data)
    }).then(function(r) { return r.json(); }).then(callback).catch(callback);
  };

  // Submit a contact form
  window.crmContact = function(data, callback) {
    fetch(API_URL + '/api/contact', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-API-Key': API_KEY },
      body: JSON.stringify(data)
    }).then(function(r) { return r.json(); }).then(callback).catch(callback);
  };

  // Auto-wire any form with data-crm="booking" or data-crm="contact"
  // Fires CRM call in background; normal PHP form submission continues uninterrupted
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('form[data-crm]').forEach(function(form) {
      form.addEventListener('submit', function() {
        var type = form.dataset.crm;
        var data = {};
        new FormData(form).forEach(function(v, k) { data[k] = v; });
        var fn = type === 'booking' ? window.crmBooking : window.crmContact;
        fn(data, function() {});
      });
    });
  });
})();
</script>
</body>
</html>
