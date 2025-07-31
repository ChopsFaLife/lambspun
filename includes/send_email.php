<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'generateEmailHTML.php';
require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

function sendOrderEmail($toEmail, $subject, $htmlContent) {
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 0; // 0 = off, 2 = basic debug, 3 = verbose
    $mail->Debugoutput = 'error_log';

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'orders@lambspunflorals.com';
        $mail->Password   = 'miin xbvu dwfh vysy';  // Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('orders@lambspunflorals.com', 'LambSpun Florals');
        $mail->addAddress($toEmail);                          // Send to customer
