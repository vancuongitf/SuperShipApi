<?php
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/Response.php');
	require_once('/storage/ssd3/122/4702122/public_html/connect/DbConnection.php');
	require_once('/storage/ssd3/122/4702122/public_html/datasource/store/StoreDataSource.php');
	date_default_timezone_set("Asia/Bangkok");

	$response = null;
	if (isset($_GET['store_id'])) {
		$storeId = $_GET['store_id'];
		$storeDataSource = new StoreDataSource(DbConnection::getConnection());
		$response = $storeDataSource->getStoreInfo($storeId);
	} else {
		$response = new Response(678, new ApiError(678, "Missing store id."));
	}
	header($response->code);
	echo json_encode($response->value);
?>