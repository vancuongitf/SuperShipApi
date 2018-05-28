<?php
    $path = getcwd();
    $paths = explode("public_html", $path);
    $basePath = $paths[0];
	require_once($basePath . 'public_html/connect/DbConnection.php');
	require_once($basePath . 'public_html/model/response/ApiError.php');
	require_once($basePath . 'public_html/datasource/shipper/ShipperDataSource.php');

	$json = file_get_contents('php://input');
    $user = json_decode($json);
    $response = null;
    if (isset($user)) {
    	$userDataSource = new ShipperDataSource(DbConnection::getConnection());
		$response = $userDataSource->createShipper($user);
    } else {
    	$response = new Response(678, new ApiError(678, "Thiếu dữ liệu"));
    }
    header($response->code);
	echo json_encode($response->value);
?>
