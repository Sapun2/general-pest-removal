<?php
/**
 * One-time DB Setup — creates tables & seeds data.
 * Visit /admin/setup once, then this page is harmless to revisit (uses CREATE IF NOT EXISTS).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_DIR')) define('BASE_DIR', dirname(__DIR__));
require_once BASE_DIR . '/includes/db.php';
require_once BASE_DIR . '/includes/seo-meta.php';

$results = [];
if ($pdo) {
    $results = setup_db_tables();
} else {
    $results = ['✗ No database connection. Check DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS in includes/db.php'];
}
?>
<!DOCTYPE html>
<html lang="en-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB Setup | General Pest Removal Admin</title>
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = {theme:{extend:{colors:{primary:'#1e3a8a',secondary:'#16a34a',accent:'#ea580c'}}}}</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-xl">
    <div class="text-center mb-8">
        <a href="/general-pest-removal/" class="inline-flex items-center gap-2 text-2xl font-black text-primary">
            <i class="fa-solid fa-bug text-secondary text-3xl"></i>
            <span>Sydney<span class="text-secondary">Pest</span> — DB Setup</span>
        </a>
    </div>
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Database Setup</h1>
        <ul class="space-y-3 mb-8">
            <?php foreach ($results as $result): ?>
            <li class="flex items-start gap-3 text-sm p-3 rounded-lg <?= strpos($result, '✓') === 0 ? 'bg-yellow-50 text-green-800' : 'bg-red-50 text-red-800' ?>">
                <i class="fa-solid <?= strpos($result, '✓') === 0 ? 'fa-check-circle text-green-600' : 'fa-times-circle text-red-600' ?> mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                <span><?= htmlspecialchars($result) ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php if ($pdo): ?>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800 mb-6">
            <strong>Default credentials:</strong> username <code>admin</code> / password <code>admin123</code><br>
            Change the password after first login for security.
        </div>
        <a href="/general-pest-removal/admin/login" class="block w-full text-center bg-primary hover:bg-blue-900 text-white font-bold py-3 rounded-lg transition">
            Go to Admin Login →
        </a>
        <?php else: ?>
        <p class="text-sm text-gray-500">Fix the database connection in <code>includes/db.php</code> then reload this page.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
