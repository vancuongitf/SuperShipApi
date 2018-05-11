<?php
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/Response.php');
	require_once('/storage/ssd3/122/4702122/public_html/connect/DbConnection.php');
	require_once('/storage/ssd3/122/4702122/public_html/datasource/order/OrderDataSource.php');
	$response = null;
	if (file_get_contents('php://input') != null) {
		$billBody = json_decode(file_get_contents('php://input'));
		$orderDataSource = new OrderDataSource(DbConnection::getConnection());
		$response = $orderDataSource->verifyPayment($billBody);
	} else {
		$response = new Response(678, new ApiError(678, "Thiếu dữ liệu."));
	}
	header($response->code);
	echo json_encode($response->value);
?>
