<?php
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/Response.php');
	require_once('/storage/ssd3/122/4702122/public_html/connect/DbConnection.php');
	require_once('/storage/ssd3/122/4702122/public_html/datasource/store/StoreDataSource.php');
	$response = null;
	if (file_get_contents('php://input') != null) {
		$optionBody = json_decode(file_get_contents('php://input'));
		$storeDataSource = new StoreDataSource(DbConnection::getConnection());
		$response = $storeDataSource->createDrinkOption($optionBody);
	} else {
		$response = new Response(678, new ApiError(678, "Thiếu dữ liệu."));
	}
	header($response->code);
	echo json_encode($response->value);
?>
