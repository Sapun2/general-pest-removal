<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_destroy();
header('Location: /general-pest-removal/admin/login');
exit;
