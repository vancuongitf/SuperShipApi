<?php
	$path = getcwd();
	$paths = explode("public_html", $path);
	$basePath = $paths[0];
	require_once($basePath . 'public_html/connect/DbConnection.php');
	require_once($basePath . 'public_html/model/response/ApiError.php');
	require_once($basePath . 'public_html/model/response/Response.php');
	require_once($basePath . 'public_html/datasource/shipper/ShipperDataSource.php');
	$response = null;
	if (isset($_POST['token']) && isset($_POST['pass']) && isset($_POST['old_pass'])) {
		$token = $_POST['token'];
		$pass = $_POST['pass'];
		$oldPass = $_POST['old_pass'];
		$shipperDataSource = new ShipperDataSource(DbConnection::getConnection());
		$response = $userDataSource->changePassword($token, $oldPass, $pass);
	} else {
		$response = new Response(678, new ApiError(678, "Thiếu dữ liệu. Vui lòng thử lại sau."));
	}
	header($response->code);
	echo json_encode($response->value);
?>
