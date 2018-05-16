<?php
	require_once('/storage/ssd3/122/4702122/public_html/connect/DbConnection.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/datasource/shipper/ShipperDataSource.php');

	$json = file_get_contents('php://input');
    $user = json_decode($json);
    $response = null;
    if (isset($user)) {
    	$userDataSource = new ShipperDataSource(DbConnection::getConnection());
		$response = $userDataSource->createShipper($user);
    } else {
    	$response = new Response(678, new ApiError(678, "Thiếu dữ liệu"));
    }
    header($response->code);
	echo json_encode($response->value);
?>
