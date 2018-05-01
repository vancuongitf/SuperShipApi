<?php

	require_once('/storage/ssd3/122/4702122/public_html/connect/DbConnection.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/datasource/user/UserDataSource.php');
	$response = null;
	if (isset($_POST['token'])) {
		$token = $_POST['token'];
		$userDataSource = new UserDataSource(DbConnection::getConnection());
		$response = $userDataSource->getUserInfo($token);
	} else {
		$response = new Response(678, new ApiError(678, "Thiếu access token."));
	}
	header($response->code);
	echo json_encode($response->value);
?>
