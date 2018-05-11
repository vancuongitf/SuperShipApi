<?php
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/Response.php');
	require_once('/storage/ssd3/122/4702122/public_html/connect/DbConnection.php');
	require_once('/storage/ssd3/122/4702122/public_html/datasource/order/OrderDataSource.php');

	$response = null;
	switch ($_SERVER['REQUEST_METHOD']) {
		case 'POST':
			if (file_get_contents('php://input') != null) {
				$order = json_decode(file_get_contents('php://input'));
				$orderDataSource = new OrderDataSource(DbConnection::getConnection());
				$response = $orderDataSource->orderDrink($order);
			} else {
				$response = new Response(678, new ApiError(678, "Thiếu dữ liệux."));
			}			
			break;
		
		case 'GET':
			if (isset($_GET['token']) && isset($_GET['id'])) {
				$token = $_GET['token'];
				$id = $_GET['id'];
				$orderDataSource = new OrderDataSource(DbConnection::getConnection());
				$response = $orderDataSource->getBillInfo($token, $id);
			} else {
				$response = new Response(678, new ApiError(678, "Thiếu dữ liệu.ssss"));
			}
			break;
	}
	header($response->code);
	echo json_encode($response->value);
?>
