<?php
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/Response.php');
	require_once('/storage/ssd3/122/4702122/public_html/connect/DbConnection.php');
	require_once('/storage/ssd3/122/4702122/public_html/datasource/order/OrderDataSource.php');
	date_default_timezone_set("Asia/Bangkok");

	$response = null;
	if (isset($_GET['token']) && isset($_GET['id'])) {
		$token = $_GET['token'];
		$id = $_GET['id'];
		$storeDataSource = new OrderDataSource(DbConnection::getConnection());
		$response = $storeDataSource->getLiveLocation($token, $id);
	} else {
		$response = new Response(678, new ApiError(678, "Missing data."));
	}
	header($response->code);
	echo json_encode($response->value);
?>