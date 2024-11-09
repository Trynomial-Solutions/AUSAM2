<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';
$phpmailer = new PHPMailer(true);
if (!isset($cfg)) require_once __DIR__ . "/config.inc.php";

try {
    //Server settings
    // $phpmailer->SMTPDebug = SMTP::DEBUG_SERVER;
    $phpmailer->isSMTP();
    $phpmailer->Host       = $cfg['email_host'];
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Username   = $cfg['email_user'];
    $phpmailer->Password   = $cfg['email_pwd'];
    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->Port       = 587;
    $phpmailer->addAddress("webmaster@meded.app");
    $phpmailer->Subject = "[AUSAM2 Error]";
    $phpmailer->From        = 'webmaster@meded.app';
    $phpmailer->isHTML(false);
} catch (Exception $e) {
    throw new RuntimeException("phpmailer init Error: {$phpmailer->ErrorInfo}");
}
