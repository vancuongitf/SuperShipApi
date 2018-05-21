<?php

	require_once('/storage/ssd3/122/4702122/public_html/connect/DbConnection.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/datasource/staff/StaffDataSource.php');
	$response = null;
	if (isset($_POST['token']) && isset($_POST['id'])) {
		$token = $_POST['token'];
		$shipperId = $_POST['id'];
		$staffDataSource = new StaffDataSource(DbConnection::getConnection());
		$response = $staffDataSource->getShiperInfo($token, $shipperId);
	} else {
		$response = new Response(678, new ApiError(678, "Thiếu access token."));
	}
	header($response->code);
	echo json_encode($response->value);
?>
