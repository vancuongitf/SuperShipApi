<?php
	$path = getcwd();
	$paths = explode("public_html", $path);
	$basePath = $paths[0];
	require_once($basePath . 'public_html/model/response/ApiError.php');
	require_once($basePath . 'public_html/model/response/Response.php');
	require_once($basePath . 'public_html/connect/DbConnection.php');
	require_once($basePath . 'public_html/datasource/shipper/ShipperDataSource.php');
	$response = null;
	if (isset($_GET['token'])) {
		$token = $_GET['token'];
		$page = 1;
		if (isset($_GET['page'])) {
			$page = (int)$_GET['page'];	
			if ($page < 1) {
				$page = 1;
			}							
		}
		$orderDataSource = new ShipperDataSource(DbConnection::getConnection());
		$response = $orderDataSource->getCheckedOrders($token, $page);
	} else {
		$response = new Response(678, new ApiError(678, "Thiếu dữ liệu."));
	}
	header($response->code);
	echo json_encode($response->value);
?>
