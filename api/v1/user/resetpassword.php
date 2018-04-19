<?php
	require_once('/storage/ssd3/122/4702122/public_html/connect/DbConnection.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/datasource/user/UserDataSource.php');
	$response = null;
	if (isset($_POST['user_id']) && isset($_POST['pass']) && isset($_POST['otp_code'])) {
		$userId = $_POST['user_id'];
		$pass = $_POST['pass'];
		$otpCode = $_POST['otp_code'];
		$userDataSource = new UserDataSource(DbConnection::getConnection());
		$response = $userDataSource->resetPassword($userId, $pass, $otpCode);
	} else {
		$apiError = new ApiError(678, "Vui lòng điền đầy đủ thông tin.");
		$response = new Response(678, $apiError);
	}
	header($response->code);
	echo json_encode($response->value);
?>
