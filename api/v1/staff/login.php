<?php
	require_once('/storage/ssd3/122/4702122/public_html/connect/DbConnection.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/datasource/staff/StaffDataSource.php');
	$response = null;
	if (isset($_POST['user']) && isset($_POST['pass'])) {
		$user = $_POST['user'];
		$pass = $_POST['pass'];
		$staffDataSource = new StaffDataSource(DbConnection::getConnection());
		$response = $staffDataSource->login($user, $pass);
	} else {
		$apiError = new ApiError(678, "Thiếu tài khoản hoặc mật khẩu.");
		$response = new Response(678, $apiError);
	}
	header($response->code);
	echo json_encode($response->value);
?>
