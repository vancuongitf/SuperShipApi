<?php
	require_once('../../../../util/check/token/CheckToken.php');
	require_once('../../../../util/const/Constant.php');
	$token = $_SERVER[Constant::ACCESS_TOKEN];
	echo $token;
?>
