<?php
	require_once('/storage/ssd3/122/4702122/public_html/connect/DbConnection.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/datasource/user/UserDataSource.php');
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
