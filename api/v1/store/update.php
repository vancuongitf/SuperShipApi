<?php
	require_once('../../../connect/DbConnection.php');
	require_once('../../../model/response/ApiError.php');
	require_once('../../../datasource/store/StoreDataSource.php');
	require_once('../../../util/TextUtil.php');

	$response = null;
	if (file_get_contents('php://input') != null) {
		$store = json_decode(file_get_contents('php://input'));
		$storeDataSource = new StoreDataSource(DbConnection::getConnection());
		$response = $storeDataSource->updateStoreInfo($store);
	} else {
		$response = new Response(678, new ApiError(678, "Thiếu dữ liệu."));
	}
	header($response->code);
	echo json_encode($response->value);
?>
