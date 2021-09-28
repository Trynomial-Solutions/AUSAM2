<?php
require ('../../inc/phpmailer.inc.php');
$phpmailer->addAddress("webmaster@henryfordem.com");
$phpmailer->isHTML(true);                                  // Set email format to HTML
$phpmailer->Subject = "[AUSAM2 Error]";
$phpmailer->Body    = $_POST['mail_send'];
$phpmailer->isHTML(false);
$phpmailer->send();
?>