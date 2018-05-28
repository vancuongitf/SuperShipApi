<?php
	require_once('../../../model/response/ApiError.php');
	require_once('../../../model/response/Response.php');
	require_once('../../../connect/DbConnection.php');
	require_once('../../../datasource/store/StoreDataSource.php');
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