<?php
	require_once('../../../../connect/DbConnection.php');
	require_once('../../../../model/response/ApiError.php');
	require_once('../../../../model/response/Response.php');
	require_once('../../../../datasource/staff/StaffDataSource.php');
	$response = null;
	if (isset($_POST['staff_id']) && isset($_POST['pass']) && isset($_POST['otp_code'])) {
		$staffId = $_POST['staff_id'];
		$pass = $_POST['pass'];
		$otpCode = $_POST['otp_code'];
		$staffDataSource = new StaffDataSource(DbConnection::getConnection());
		$response = $staffDataSource->resetPassword($staffId, $pass, $otpCode);
	} else {
		$response = Response::getMessageResponseWithMessage("Vui lòng điền đầy đủ thông tin.");
	}
	header($response->code);
	echo json_encode($response->value);
?>
