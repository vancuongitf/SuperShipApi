<?php
	require_once('../../../../model/response/ApiError.php');
	require_once('../../../../model/response/Response.php');
	require_once('../../../../connect/DbConnection.php');
	require_once('../../../../datasource/order/OrderDataSource.php');
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
		$orderDataSource = new OrderDataSource(DbConnection::getConnection());
		$response = $orderDataSource->getUserOrders($token, $page);
	} else {
		$response = new Response(678, new ApiError(678, "Thiếu dữ liệu."));
	}
	header($response->code);
	echo json_encode($response->value);
?>
