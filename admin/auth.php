<?php
/**
 * Admin Auth Middleware
 * Include at the top of every admin page (after session_start).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: /admin/login');
    exit;
}
