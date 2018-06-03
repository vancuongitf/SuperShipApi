<?php
	require_once('../../../../connect/DbConnection.php');
	require_once('../../../../model/response/ApiError.php');
	require_once('../../../../model/response/Response.php');
	require_once('../../../../datasource/shipper/ShipperDataSource.php');
	$response = null;
	if (isset($_GET['email'])) {
		$email = $_GET['email'];
		$shipperDataSource = new ShipperDataSource(DbConnection::getConnection());
		$response = $shipperDataSource->requestResetPass($email);
	} else {
		$response = Response::getMessageResponseWithMessage("Thiếu dữ liệu.");
	}
	header($response->code);
	echo json_encode($response->value);
?>
