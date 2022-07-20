<?php
require_once __DIR__."/phpmailer.inc.php";
$phpmailer->Body    = $_POST['mail_send'];
$phpmailer->send();
?>