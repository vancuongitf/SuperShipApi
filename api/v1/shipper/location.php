<?php
	require_once('/storage/ssd3/122/4702122/public_html/connect/DbConnection.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/datasource/shipper/ShipperDataSource.php');
	require_once('/storage/ssd3/122/4702122/public_html/util/TextUtil.php');

	$json = file_get_contents('php://input');
    $location = json_decode($json);
    $response = null;
    if (isset($location)) {
    	$userDataSource = new ShipperDataSource(DbConnection::getConnection());
		$response = $userDataSource->setCurrentLocation($location);
    } else {
    	$apiError = new ApiError(678, "Missing data.");
		$response = new Response(678, $apiError);
    }
    header($response->code);
	echo json_encode($response->value);
?>
