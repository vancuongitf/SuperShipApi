<?php
	$path = getcwd();
	$paths = explode("public_html", $path);
	$basePath = $paths[0];
	require_once($basePath . 'public_html/connect/DbConnection.php');
	require_once($basePath . 'public_html/model/response/ApiError.php');
	require_once($basePath . 'public_html/datasource/user/UserDataSource.php');
	if (isset($_GET['user']) && isset($_GET['active_key'])) {
		$user = $_GET['user'];
		$key = $_GET['active_key'];
		$userDataSource = new UserDataSource(DbConnection::getConnection());
		$response = $userDataSource->activeUser($user, $key);
		$message = $response->value->message;
		echo '<script type="text/javascript">'; 
		echo 'alert("' . $message . '");'; 
		echo 'window.location.href = "https://vnshipperman.000webhostapp.com";';
		echo '</script>';
	}
?>