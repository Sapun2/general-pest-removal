<?php
/**
 * Site-wide Configuration — General Pest Removal
 */
if (defined('SITE_BASE_URL')) return;

// Auto-detect local MAMP/localhost environment
$_gpr_local = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1']);
define('SITE_BASE_URL',  'https://generalpestremoval.com');
define('SITE_BASE_PATH', $_gpr_local ? '/general-pest-removal' : '');
unset($_gpr_local);
define('SITE_NAME',      'General Pest Removal');
define('SITE_PHONE',     '(02) 8155 0198');
define('SITE_PHONE_RAW', '+61281550198');
define('SITE_EMAIL',     'info@generalpestremoval.com');
define('ADMIN_EMAIL',    'info@generalpestremoval.com');
