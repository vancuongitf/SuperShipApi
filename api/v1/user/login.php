<?php
	require_once('../../../connect/DbConnection.php');
	require_once('../../../model/response/ApiError.php');
	require_once('../../../datasource/user/UserDataSource.php');
	$response = null;
	if (isset($_POST['user']) && isset($_POST['pass'])) {
		$user = $_POST['user'];
		$pass = $_POST['pass'];
		$userDataSource = new UserDataSource(DbConnection::getConnection());
		$response = $userDataSource->login($user, $pass);
	} else {
		$apiError = new ApiError(678, "Thiếu tài khoản hoặc mật khẩu.");
		$response = new Response(678, $apiError);
	}
	header($response->code);
	echo json_encode($response->value);
?>
