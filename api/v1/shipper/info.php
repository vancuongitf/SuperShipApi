<?php
	$path = getcwd();
    $paths = explode("public_html", $path);
    $basePath = $paths[0];
	require_once($basePath . 'public_html/connect/DbConnection.php');
	require_once($basePath . 'public_html/model/response/ApiError.php');
	require_once($basePath . 'public_html/datasource/shipper/ShipperDataSource.php');
	$response = null;
	if (isset($_POST['token'])) {
		$token = $_POST['token'];
		$userDataSource = new ShipperDataSource(DbConnection::getConnection());
		$response = $userDataSource->getShiperInfo($token);
	} else {
		$response = new Response(678, new ApiError(678, "Thiếu access token."));
	}
	header($response->code);
	echo json_encode($response->value);
?>
