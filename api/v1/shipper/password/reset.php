<?php
	require_once('../../../../connect/DbConnection.php');
	require_once('../../../../model/response/ApiError.php');
	require_once('../../../../model/response/Response.php');
	require_once('../../../../datasource/shipper/ShipperDataSource.php');
	$response = null;
	if (isset($_POST['shipper_id']) && isset($_POST['pass']) && isset($_POST['otp_code'])) {
		$shipperId = $_POST['shipper_id'];
		$pass = $_POST['pass'];
		$otpCode = $_POST['otp_code'];
		$shipperDataSource = new ShipperDataSource(DbConnection::getConnection());
		$response = $shipperDataSource->resetPassword($shipperId, $pass, $otpCode);
	} else {
		$response = Response::getMessageResponseWithMessage("Vui lòng điền đầy đủ thông tin.");
	}
	header($response->code);
	echo json_encode($response->value);
?>
