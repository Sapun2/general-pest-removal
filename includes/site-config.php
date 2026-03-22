<?php
/**
 * Site Configuration Loader
 * Reads all settings from the site_config DB table.
 * Falls back to config.php constants if DB is unavailable or a key is missing.
 */

function load_site_config(): array
{
    global $pdo;

    $defaults = [
        // Business
        'business_name'        => defined('SITE_NAME')      ? SITE_NAME      : 'General Pest Removal',
        'tagline'              => "Sydney & Brisbane's Trusted Pest Removal Specialists",
        // Logo
        'logo_type'            => 'text',      // 'text' | 'image'
        'logo_icon'            => 'fa-bug',
        'logo_text_primary'    => 'General',
        'logo_text_secondary'  => 'Pest',
        'logo_image_url'       => '',
        // Phones
        'phone_primary'        => defined('SITE_PHONE')     ? SITE_PHONE     : '(02) 8155 0198',
        'phone_primary_raw'    => defined('SITE_PHONE_RAW') ? SITE_PHONE_RAW : '+61281550198',
        'phone_secondary'      => '',
        'phone_secondary_raw'  => '',
        // Contact
        'email_primary'        => defined('SITE_EMAIL')     ? SITE_EMAIL     : 'info@generalpestremoval.com',
        'email_admin'          => defined('ADMIN_EMAIL')    ? ADMIN_EMAIL    : 'info@generalpestremoval.com',
        'address'              => 'Sydney, NSW & Brisbane, QLD, Australia',
        // Hours
        'hours_weekday'        => 'Mon–Sat, 7am–8pm',
        'hours_emergency'      => '24/7 Emergency Line',
        // Social
        'social_facebook'      => '',
        'social_instagram'     => '',
        'social_tiktok'        => '',
        'social_google'        => '',
        'social_yelp'          => '',
        // SMTP / Email
        'smtp_host'            => '',
        'smtp_port'            => '587',
        'smtp_user'            => '',
        'smtp_pass'            => '',
        'smtp_encryption'      => 'tls',   // tls | ssl | none
        'smtp_from_name'       => '',
        'smtp_from_email'      => '',
        // Tracking codes
        'gtm_id'               => '',      // GTM-XXXXXXX
        'ga4_id'               => '',      // G-XXXXXXXXXX
        'gads_id'              => '',      // AW-XXXXXXXXXX
        'gads_label'           => '',      // conversion label
        'meta_pixel_id'        => '',      // numeric ID
        'gsc_verification'     => '',      // Google Search Console meta content value
    ];

    if (!$pdo) {
        return $defaults;
    }

    try {
        $rows = $pdo->query("SELECT config_key, config_value FROM site_config")
                    ->fetchAll(PDO::FETCH_KEY_PAIR);
        foreach ($rows as $key => $value) {
            if ($value !== null && $value !== '') {
                $defaults[$key] = $value;
            }
        }
    } catch (PDOException $e) {
        // Table doesn't exist yet — use defaults
    }

    return $defaults;
}
