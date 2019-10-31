<?php
if (!isset($dbi)) {
	$dbi=new MySQLi("<server>", "<username>", "<password>", "<db_name>");
	if ($dbi->connect_errno) die ("Failed to connect");
	}
?>