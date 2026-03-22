<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_destroy();
header('Location: /sydney-pest-removal/admin/login');
exit;
