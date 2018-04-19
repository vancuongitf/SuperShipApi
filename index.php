<?php
	$email = $_GET['email'];
	$v = "/[a-zA-Z0-9_-.+]+@[a-zA-Z0-9-]+.[a-zA-Z]+/";
	echo preg_match($v, $email);
?>