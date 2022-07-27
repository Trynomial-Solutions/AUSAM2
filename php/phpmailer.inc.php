<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__.'/../vendor/autoload.php';
$phpmailer = new PHPMailer(true);

try {
    //Server settings
    // $phpmailer->SMTPDebug = SMTP::DEBUG_SERVER;
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'secure.emailsrvr.com';
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Username   = 'noreply@mwconsulting.dev';
    $phpmailer->Password   = 'a36L#Qo*yFn6oWpGzE';
    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->Port       = 587;
    $phpmailer->addAddress("webmaster@meded.app");
    $phpmailer->Subject = "[AUSAM2 Error]";
    $phpmailer->isHTML(false);
} catch (Exception $e) {
    throw new RuntimeException("phpmailer init Error: {$phpmailer->ErrorInfo}");
}
?>