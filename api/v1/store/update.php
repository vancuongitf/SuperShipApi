<?php
	require_once('/storage/ssd3/122/4702122/public_html/connect/DbConnection.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/datasource/store/StoreDataSource.php');
	require_once('/storage/ssd3/122/4702122/public_html/util/TextUtil.php');

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
