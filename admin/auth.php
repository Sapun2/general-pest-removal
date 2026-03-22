<?php
/**
 * Admin Auth Middleware
 * Include at the top of every admin page (after session_start).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: /general-pest-removal/admin/login');
    exit;
}
