<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Already logged in — redirect to dashboard
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: /general-pest-removal/admin');
    exit;
}

if (!defined('BASE_DIR')) define('BASE_DIR', dirname(__DIR__));
require_once BASE_DIR . '/includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password && isset($pdo)) {
        try {
            $stmt = $pdo->prepare("SELECT password_hash FROM admin_users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && password_verify($password, $row['password_hash'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username']  = $username;
                header('Location: /general-pest-removal/admin');
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            $error = 'Database error. Please run DB setup first.';
        }
    } else {
        $error = 'Please enter both username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | General Pest Removal</title>
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = {theme:{extend:{colors:{primary:'#1e3a8a',secondary:'#16a34a',accent:'#ea580c'}}}}</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <a href="/general-pest-removal/" class="inline-flex items-center gap-2 text-2xl font-black text-primary">
            <i class="fa-solid fa-bug text-secondary text-3xl"></i>
            <span>Sydney<span class="text-secondary">Pest</span> Admin</span>
        </a>
    </div>
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6 text-center">Admin Login</h1>
        <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        <form method="POST" class="space-y-5">
            <div>
                <label for="username" class="block text-sm font-bold text-gray-700 mb-2">Username</label>
                <input type="text" id="username" name="username" required autocomplete="username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none transition">
            </div>
            <div>
                <label for="password" class="block text-sm font-bold text-gray-700 mb-2">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password"
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary outline-none transition">
            </div>
            <button type="submit" class="w-full bg-primary hover:bg-blue-900 text-white font-bold py-3 rounded-lg transition">
                Log In
            </button>
        </form>
        <p class="text-xs text-gray-400 text-center mt-6">
            First time? <a href="/general-pest-removal/admin/setup" class="text-primary underline">Run DB Setup</a> to create the admin account.
        </p>
    </div>
    <p class="text-center mt-4 text-sm text-gray-500">
        <a href="/general-pest-removal/" class="hover:text-primary transition">← Back to Website</a>
    </p>
</div>
</body>
</html>
