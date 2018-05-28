<?php
	$path = getcwd();
    $paths = explode("public_html", $path);
    $basePath = $paths[0];
	require_once($basePath . 'public_html/connect/DbConnection.php');
	require_once($basePath . 'public_html/model/response/ApiError.php');
	require_once($basePath . 'public_html/datasource/shipper/ShipperDataSource.php');
	$response = null;
	if (isset($_POST['user']) && isset($_POST['pass'])) {
		$user = $_POST['user'];
		$pass = $_POST['pass'];
		$userDataSource = new ShipperDataSource(DbConnection::getConnection());
		$response = $userDataSource->login($user, $pass);
	} else {
		$apiError = new ApiError(678, "Thiếu tài khoản hoặc mật khẩu.");
		$response = new Response(678, $apiError);
	}
	header($response->code);
	echo json_encode($response->value);
?>