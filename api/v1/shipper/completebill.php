<?php
	require_once('/storage/ssd3/122/4702122/public_html/connect/DbConnection.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/datasource/shipper/ShipperDataSource.php');
	require_once('/storage/ssd3/122/4702122/public_html/util/TextUtil.php');

	$json = file_get_contents('php://input');
    $bill = json_decode($json);
    $response = null;
    if (isset($bill)) {
    	$userDataSource = new ShipperDataSource(DbConnection::getConnection());
		$response = $userDataSource->completedBill($bill->token, $bill->bill_id, $bill->confirm_code);
    } else {
    	$apiError = new ApiError(678, "Missing data.");
		$response = new Response(678, $apiError);
    }
    header($response->code);
	echo json_encode($response->value);
?>
