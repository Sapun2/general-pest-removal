<?php
/**
 * Booking Form Handler
 * POST → validates → saves to DB → sends emails → redirects to /thank-you
 */
if (!defined('BASE_DIR')) define('BASE_DIR', __DIR__);
require_once BASE_DIR . '/config.php';
require_once BASE_DIR . '/includes/db.php';
require_once BASE_DIR . '/includes/site-config.php';

// Load site config so mailer can read SMTP settings
if (!isset($GLOBALS['site_config'])) {
    $GLOBALS['site_config'] = load_site_config();
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . SITE_BASE_PATH . '/booking');
    exit;
}

// ── CSRF ────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) session_start();

if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    $_SESSION['flash_error'] = 'Security token mismatch. Please try again.';
    header('Location: ' . SITE_BASE_PATH . '/booking');
    exit;
}
unset($_SESSION['csrf_token']);

// ── Sanitize ─────────────────────────────────────────────────────
$fields = ['first_name','last_name','phone','email','street_address','pest_type','message'];
$data   = [];
foreach ($fields as $field) {
    $data[$field] = htmlspecialchars(trim($_POST[$field] ?? ''), ENT_QUOTES, 'UTF-8');
}

// ── Validate ─────────────────────────────────────────────────────
$errors = [];
if (empty($data['first_name']))     $errors[] = 'First name is required.';
if (empty($data['last_name']))      $errors[] = 'Last name is required.';
if (empty($data['phone']))          $errors[] = 'Phone number is required.';
if (empty($data['street_address'])) $errors[] = 'Street address is required.';
if (empty($data['pest_type']))      $errors[] = 'Please select a pest type.';
if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if (!empty($errors)) {
    $_SESSION['flash_error'] = implode(' ', $errors);
    $_SESSION['form_data']   = $data;
    header('Location: ' . SITE_BASE_PATH . '/booking');
    exit;
}

// ── Save to DB ───────────────────────────────────────────────────
if (!$pdo) {
    $_SESSION['flash_error'] = 'Service temporarily unavailable. Please call us directly at ' . SITE_PHONE . '.';
    header('Location: ' . SITE_BASE_PATH . '/booking');
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO bookings (first_name, last_name, phone, email, city, pest_type, property_type, message, status)
         VALUES (:first_name, :last_name, :phone, :email, :city, :pest_type, :property_type, :message, 'new')"
    );
    $stmt->execute([
        ':first_name'    => $data['first_name'],
        ':last_name'     => $data['last_name'],
        ':phone'         => $data['phone'],
        ':email'         => $data['email'],
        ':city'          => $data['street_address'],
        ':pest_type'     => $data['pest_type'],
        ':property_type' => '',
        ':message'       => $data['message'],
    ]);

    // ── Send emails ──────────────────────────────────────────────
    require_once BASE_DIR . '/includes/mailer.php';

    $cfg         = $GLOBALS['site_config'];
    $biz_name    = $cfg['business_name'] ?? 'General Pest Removal';
    $admin_email = $cfg['email_admin']   ?? $cfg['email_primary'] ?? ADMIN_EMAIL ?? '';
    $full_name   = $data['first_name'] . ' ' . $data['last_name'];
    $site_url    = defined('SITE_BASE_URL') ? SITE_BASE_URL : '';

    // — Admin alert —
    if ($admin_email) {
        $admin_body = mail_template(
            'New Booking Received',
            '<p>A new booking has been submitted through the website.</p>
            <table style="width:100%;border-collapse:collapse;font-size:14px;">
              <tr><td style="padding:8px 12px;background:#f8fafc;border:1px solid #e2e8f0;font-weight:600;width:35%">Name</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">' . htmlspecialchars($full_name) . '</td></tr>
              <tr><td style="padding:8px 12px;background:#f8fafc;border:1px solid #e2e8f0;font-weight:600;">Phone</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">' . htmlspecialchars($data['phone']) . '</td></tr>
              <tr><td style="padding:8px 12px;background:#f8fafc;border:1px solid #e2e8f0;font-weight:600;">Email</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">' . htmlspecialchars($data['email']) . '</td></tr>
              <tr><td style="padding:8px 12px;background:#f8fafc;border:1px solid #e2e8f0;font-weight:600;">Address</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">' . htmlspecialchars($data['street_address']) . '</td></tr>
              <tr><td style="padding:8px 12px;background:#f8fafc;border:1px solid #e2e8f0;font-weight:600;">Pest Type</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">' . htmlspecialchars($data['pest_type']) . '</td></tr>
              <tr><td style="padding:8px 12px;background:#f8fafc;border:1px solid #e2e8f0;font-weight:600;">Message</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">' . nl2br(htmlspecialchars($data['message'])) . '</td></tr>
            </table>
            <p style="margin-top:20px;">
              <a href="' . $site_url . '/general-pest-removal/admin/bookings" style="background:#1e3a8a;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;">View in Admin Panel</a>
            </p>',
            $biz_name
        );
        send_mail($admin_email, 'Admin', "New Booking: {$data['pest_type']} — {$full_name}", $admin_body);
    }

    // — Customer confirmation —
    if (!empty($data['email'])) {
        $cust_body = mail_template(
            'Your Booking is Confirmed',
            '<p>Hi ' . htmlspecialchars($data['first_name']) . ',</p>
            <p>Thank you for booking with <strong>' . htmlspecialchars($biz_name) . '</strong>! We have received your request and will contact you within 2 hours to confirm your appointment.</p>
            <table style="width:100%;border-collapse:collapse;font-size:14px;margin:16px 0;">
              <tr><td style="padding:8px 12px;background:#f8fafc;border:1px solid #e2e8f0;font-weight:600;width:40%">Service Requested</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">' . htmlspecialchars($data['pest_type']) . '</td></tr>
              <tr><td style="padding:8px 12px;background:#f8fafc;border:1px solid #e2e8f0;font-weight:600;">Address</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">' . htmlspecialchars($data['street_address']) . '</td></tr>
            </table>
            <p>If you have any questions, reply to this email or call us at <strong>' . htmlspecialchars($cfg['phone_primary'] ?? SITE_PHONE) . '</strong>.</p>
            <p style="color:#64748b;font-size:13px;">NSW Licensed &amp; Fully Insured &mdash; ' . htmlspecialchars($biz_name) . '</p>',
            $biz_name
        );
        send_mail($data['email'], $full_name, "Your Pest Control Booking — {$biz_name}", $cust_body);
    }

    unset($_SESSION['form_data']);
    header('Location: ' . SITE_BASE_PATH . '/thank-you');
    exit;

} catch (PDOException $e) {
    error_log('Booking insert error: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'We could not process your request. Please call us at ' . SITE_PHONE . ' or try again.';
    header('Location: ' . SITE_BASE_PATH . '/booking');
    exit;
}
