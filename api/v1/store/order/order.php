<?php
	require_once('../../../../model/response/ApiError.php');
	require_once('../../../../model/response/Response.php');
	require_once('../../../../connect/DbConnection.php');
	require_once('../../../../datasource/order/OrderDataSource.php');
	require_once('../../../../datasource/shipper/ShipperDataSource.php');
	require_once('../../../../datasource/staff/StaffDataSource.php');

	$response = null;
	switch ($_SERVER['REQUEST_METHOD']) {
		case 'POST':
			if (file_get_contents('php://input') != null) {
				$order = json_decode(file_get_contents('php://input'));
				$orderDataSource = new OrderDataSource(DbConnection::getConnection());
				$response = $orderDataSource->orderDrink($order);
			} else {
				$response = new Response(678, new ApiError(678, "Thiếu dữ liệu."));
			}			
			break;
		
		case 'GET':
			if (isset($_GET['token']) && isset($_GET['id'])) {
				$token = $_GET['token'];
				$id = $_GET['id'];
				$orderDataSource = null;
				$module = 0;

				if (isset($_GET['module'])) {
					$module = (int)$_GET['module'];
				}

				switch ($module) {
					case 0:
						$orderDataSource = new OrderDataSource(DbConnection::getConnection());
						break;
					
					case 1:
						$orderDataSource = new ShipperDataSource(DbConnection::getConnection());
						break;
					case 2:
						$orderDataSource = new StaffDataSource(DbConnection::getConnection());
						break;
				}
				$response = $orderDataSource->getBillInfo($token, $id);
			} else {
				$response = new Response(678, new ApiError(678, "Thiếu dữ liệu."));
			}
			break;
	}
	header($response->code);
	echo json_encode($response->value);
?>
