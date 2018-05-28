<?php
	$path = getcwd();
    $paths = explode("public_html", $path);
    $basePath = $paths[0];
	require_once($basePath . 'public_html/model/response/ApiError.php');
	require_once($basePath . 'public_html/model/response/Response.php');
	require_once($basePath . 'public_html/connect/DbConnection.php');
	require_once($basePath . 'public_html/datasource/shipper/ShipperDataSource.php');
	$response = null;
	if (file_get_contents('php://input') != null) {
		$billBody = json_decode(file_get_contents('php://input'));
		$orderDataSource = new ShipperDataSource(DbConnection::getConnection());
		$response = $orderDataSource->verifyCash($billBody);
	} else {
		$response = new Response(678, new ApiError(678, "Thiếu dữ liệu."));
	}
	header($response->code);
	echo json_encode($response->value);
?>
