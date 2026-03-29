<?php
/**
 * Contact Form Handler
 * POST → validates → saves to DB → sends emails → redirects to /contact
 */
if (!defined('BASE_DIR')) define('BASE_DIR', __DIR__);
require_once BASE_DIR . '/config.php';
require_once BASE_DIR . '/includes/db.php';
require_once BASE_DIR . '/includes/site-config.php';

if (!isset($GLOBALS['site_config'])) {
    $GLOBALS['site_config'] = load_site_config();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . SITE_BASE_PATH . '/contact');
    exit;
}

// ── CSRF ─────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) session_start();

if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    $_SESSION['flash_error'] = 'Security token mismatch. Please try again.';
    header('Location: ' . SITE_BASE_PATH . '/contact');
    exit;
}
unset($_SESSION['csrf_token']);

// ── Sanitize ─────────────────────────────────────────────────────
$name    = htmlspecialchars(trim($_POST['name']    ?? ''), ENT_QUOTES, 'UTF-8');
$email   = htmlspecialchars(trim($_POST['email']   ?? ''), ENT_QUOTES, 'UTF-8');
$phone   = htmlspecialchars(trim($_POST['phone']   ?? ''), ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');

// ── Validate ─────────────────────────────────────────────────────
$errors = [];
if (empty($name))    $errors[] = 'Your name is required.';
if (empty($email))   $errors[] = 'Your email address is required.';
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email.';
if (empty($message)) $errors[] = 'Please enter a message.';

if (!empty($errors)) {
    $_SESSION['flash_error'] = implode(' ', $errors);
    $_SESSION['form_data']   = compact('name', 'email', 'phone', 'message');
    header('Location: ' . SITE_BASE_PATH . '/contact');
    exit;
}

// ── Save to DB ───────────────────────────────────────────────────
if (!$pdo) {
    $_SESSION['flash_error'] = 'Service temporarily unavailable. Please email us at ' . SITE_EMAIL . ' directly.';
    header('Location: ' . SITE_BASE_PATH . '/contact');
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO contacts (name, email, phone, message, status)
         VALUES (:name, :email, :phone, :message, 'unread')"
    );
    $stmt->execute([
        ':name'    => $name,
        ':email'   => $email,
        ':phone'   => $phone,
        ':message' => $message,
    ]);

    // ── Send emails ──────────────────────────────────────────────
    require_once BASE_DIR . '/includes/mailer.php';

    $cfg         = $GLOBALS['site_config'];
    $biz_name    = $cfg['business_name'] ?? 'General Pest Removal';
    $admin_email = $cfg['email_admin']   ?? $cfg['email_primary'] ?? ADMIN_EMAIL ?? '';
    $site_url    = defined('SITE_BASE_URL') ? SITE_BASE_URL : '';

    // — Admin alert —
    if ($admin_email) {
        $admin_body = mail_template(
            'New Contact Message',
            '<p>A visitor has sent a message through the website contact form.</p>
            <table style="width:100%;border-collapse:collapse;font-size:14px;">
              <tr><td style="padding:8px 12px;background:#f8fafc;border:1px solid #e2e8f0;font-weight:600;width:35%">Name</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">' . htmlspecialchars($name) . '</td></tr>
              <tr><td style="padding:8px 12px;background:#f8fafc;border:1px solid #e2e8f0;font-weight:600;">Email</td><td style="padding:8px 12px;border:1px solid #e2e8f0;"><a href="mailto:' . htmlspecialchars($email) . '" style="color:#1e3a8a;">' . htmlspecialchars($email) . '</a></td></tr>
              <tr><td style="padding:8px 12px;background:#f8fafc;border:1px solid #e2e8f0;font-weight:600;">Phone</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">' . (empty($phone) ? '—' : htmlspecialchars($phone)) . '</td></tr>
              <tr><td style="padding:8px 12px;background:#f8fafc;border:1px solid #e2e8f0;font-weight:600;">Message</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">' . nl2br(htmlspecialchars($message)) . '</td></tr>
            </table>
            <p style="margin-top:20px;">
              <a href="' . $site_url . '/admin/contacts" style="background:#1e3a8a;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;">View in Admin Panel</a>
            </p>',
            $biz_name
        );
        send_mail($admin_email, 'Admin', "New Message from {$name}", $admin_body);
    }

    // — Customer acknowledgement —
    $cust_body = mail_template(
        'We Received Your Message',
        '<p>Hi ' . htmlspecialchars($name) . ',</p>
        <p>Thank you for reaching out to <strong>' . htmlspecialchars($biz_name) . '</strong>. We have received your message and will get back to you within <strong>24 hours</strong>.</p>
        <p>If your matter is urgent, please call us directly at <strong>' . htmlspecialchars($cfg['phone_primary'] ?? SITE_PHONE) . '</strong>.</p>
        <blockquote style="border-left:3px solid #e2e8f0;margin:16px 0;padding:12px 16px;background:#f8fafc;font-style:italic;color:#475569;border-radius:4px;">'
            . nl2br(htmlspecialchars($message)) .
        '</blockquote>
        <p style="color:#64748b;font-size:13px;">NSW Licensed &amp; Fully Insured &mdash; ' . htmlspecialchars($biz_name) . '</p>',
        $biz_name
    );
    send_mail($email, $name, "We received your message — {$biz_name}", $cust_body);

    unset($_SESSION['form_data']);
    header('Location: ' . SITE_BASE_PATH . '/thank-you');
    exit;

} catch (PDOException $e) {
    error_log('Contact insert error: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Could not send your message. Please email us at ' . SITE_EMAIL . ' directly.';
    header('Location: ' . SITE_BASE_PATH . '/contact');
    exit;
}
