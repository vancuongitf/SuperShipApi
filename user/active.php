<?php
	$path = getcwd();
	$paths = explode("public_html", $path);
	$basePath = $paths[0];
	require_once($basePath . 'public_html/connect/DbConnection.php');
	require_once($basePath . 'public_html/model/response/ApiError.php');
	require_once($basePath . 'public_html/datasource/user/UserDataSource.php');
	if (isset($_GET['user']) && isset($_GET['active_key'])) {
		$user = $_GET['user'];
		$key = (int) $_GET['active_key'];
		if ($key == -1) {
			header("location: https://vnshipperman.000webhostapp.com/");
		} else {
			$userDataSource = new UserDataSource(DbConnection::getConnection());
			$message = $userDataSource->activeUser($user, $key);
			echo $message->message;
		}
	} else {
			header("location: https://vnshipperman.000webhostapp.com/");
	}
?>
