<?php
$mysqli = new mysqli('localhost', 'mod5', 'calendar', 'module_5');

if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}
?>