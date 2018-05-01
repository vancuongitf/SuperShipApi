<?php
	require_once('/storage/ssd3/122/4702122/public_html/connect/DbConnection.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/datasource/user/UserDataSource.php');
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
