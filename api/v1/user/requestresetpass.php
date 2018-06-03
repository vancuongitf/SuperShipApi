<?php
	require_once('../../../connect/DbConnection.php');
	require_once('../../../model/response/ApiError.php');
	require_once('../../../model/response/Response.php');
	require_once('../../../datasource/user/UserDataSource.php');
	$response = null;
	if (isset($_GET['email'])) {
		$email = $_GET['email'];
		$userDataSource = new UserDataSource(DbConnection::getConnection());
		$response = $userDataSource->requestResetPass($email);
	} else {
		$apiError = new ApiError(678, "Vui lòng nhập email.");
		$response = new Response(678, $apiError);
	}
	header($response->code);
	echo json_encode($response->value);
?>
