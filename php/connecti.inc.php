<?php
if (!isset($dbi)) {
	$dbi=new MySQLi("SERVER", "USERNAME", "PASSWORD", "DATABASE");
	if ($dbi->connect_errno) die ("Failed to connect");
	}
?>