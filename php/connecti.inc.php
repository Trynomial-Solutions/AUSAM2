<?php
if (!isset($cfg)) require_once __DIR__."/config.inc.php";
if (!isset($dbi)) {
	$dbi=new MySQLi($cfg['mysql_host'], $cfg['mysql_user'], $cfg['mysql_pwd'], $cfg['mysql_db']);
	$dbi->set_charset("utf8mb4");
    $dbi->query("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $dbi->query("SET time_zone = 'America/Detroit';");
	if ($dbi->connect_errno) die ("Failed to connect");
	}
?>