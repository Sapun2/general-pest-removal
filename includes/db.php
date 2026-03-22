<?php
/**
 * Database Connection (PDO)
 * ─────────────────────────────────────────────────────────────
 * MAMP MySQL defaults:  host 127.0.0.1, port 8889, user root, pass root
 * XAMPP / production:   change DB_PORT to 3306 and credentials below
 * ─────────────────────────────────────────────────────────────
 */

// Load site-wide config (sets SITE_BASE_URL etc.)
if (!defined('SITE_BASE_URL')) {
    require_once dirname(__DIR__) . '/config.php';
}

// DB credentials — edit these or move to a .env file for production
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'general_pest_db');
define('DB_USER', 'pestuser');
define('DB_PASS', 'PestDB@Secure2024!');

try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME),
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    $pdo = null;
    error_log('General Pest Removal DB connection failed: ' . $e->getMessage());
}

// Load site config from DB into global array (used by header, footer, templates)
require_once __DIR__ . '/site-config.php';
$GLOBALS['site_config'] = load_site_config();
