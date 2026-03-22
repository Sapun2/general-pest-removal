<?php
/**
 * Mailer Helper — PHPMailer wrapper using site_config SMTP settings.
 * Usage:
 *   $ok = send_mail('to@example.com', 'To Name', 'Subject', '<h1>HTML body</h1>', 'Plain text body');
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

if (!defined('BASE_DIR')) define('BASE_DIR', dirname(__DIR__));
require_once BASE_DIR . '/vendor/autoload.php';

/**
 * Send an email using the SMTP settings stored in site_config.
 *
 * @param string $to_email     Recipient email address
 * @param string $to_name      Recipient display name
 * @param string $subject      Email subject
 * @param string $html_body    HTML email body
 * @param string $plain_body   Plain-text fallback (auto-generated from HTML if empty)
 * @return bool                True on success, false on failure (error logged)
 */
function send_mail(string $to_email, string $to_name, string $subject, string $html_body, string $plain_body = ''): bool
{
    $cfg = $GLOBALS['site_config'] ?? [];

    $smtp_host       = $cfg['smtp_host']       ?? '';
    $smtp_port       = (int) ($cfg['smtp_port'] ?? 587);
    $smtp_user       = $cfg['smtp_user']       ?? '';
    $smtp_pass       = $cfg['smtp_pass']       ?? '';
    $smtp_encryption = $cfg['smtp_encryption'] ?? 'tls'; // tls | ssl | none
    $from_name       = $cfg['smtp_from_name']  ?? ($cfg['business_name'] ?? 'General Pest Removal');
    $from_email      = $cfg['smtp_from_email'] ?? ($cfg['email_primary'] ?? '');

    // If no SMTP configured, fall back to PHP mail()
    if (empty($smtp_host) || empty($smtp_user)) {
        $plain = $plain_body ?: strip_tags($html_body);
        $headers = "From: {$from_name} <{$from_email}>\r\nContent-Type: text/plain; charset=UTF-8\r\n";
        $ok = @mail($to_email, $subject, $plain, $headers);
        if (!$ok) {
            error_log("mailer: mail() failed — to:{$to_email} subject:{$subject}");
        }
        return $ok;
    }

    $mail = new PHPMailer(true);
    try {
        // ── Server settings ──────────────────────────────────
        $mail->isSMTP();
        $mail->Host       = $smtp_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp_user;
        $mail->Password   = $smtp_pass;
        $mail->Port       = $smtp_port;

        if ($smtp_encryption === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($smtp_encryption === 'none') {
            $mail->SMTPSecure = false;
            $mail->SMTPAuth   = false;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ],
        ];

        // ── From / To ────────────────────────────────────────
        $mail->setFrom($from_email, $from_name);
        $mail->addAddress($to_email, $to_name);
        $mail->addReplyTo($from_email, $from_name);

        // ── Content ──────────────────────────────────────────
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $html_body;
        $mail->AltBody = $plain_body ?: strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $html_body));

        $mail->send();
        return true;

    } catch (PHPMailerException $e) {
        error_log('mailer: PHPMailer error — ' . $mail->ErrorInfo . ' — to:' . $to_email);
        return false;
    }
}

/**
 * Build a styled HTML email wrapper — keeps all emails on-brand.
 */
function mail_template(string $heading, string $body_html, string $business_name = ''): string
{
    $cfg  = $GLOBALS['site_config'] ?? [];
    $name = $business_name ?: ($cfg['business_name'] ?? 'General Pest Removal');
    $year = date('Y');

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{$heading}</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Inter,Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f5;padding:40px 0;">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;max-width:600px;width:100%;">

        <!-- Header -->
        <tr>
          <td style="background:#1e3a8a;padding:28px 32px;">
            <p style="margin:0;font-size:20px;font-weight:700;color:#ffffff;">{$name}</p>
            <p style="margin:4px 0 0;font-size:13px;color:#93c5fd;">{$heading}</p>
          </td>
        </tr>

        <!-- Body -->
        <tr>
          <td style="padding:32px;color:#1e293b;font-size:14px;line-height:1.7;">
            {$body_html}
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background:#f8fafc;padding:20px 32px;border-top:1px solid #e2e8f0;">
            <p style="margin:0;font-size:12px;color:#94a3b8;">&copy; {$year} {$name}. All rights reserved.</p>
          </td>
        </tr>

      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
}
