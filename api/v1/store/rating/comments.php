<?php
	require_once('../../../../connect/DbConnection.php');
	require_once('../../../../datasource/store/StoreDataSource.php');
	require_once('../../../../model/response/ApiError.php');
	require_once('../../../../model/response/Response.php');
	$response = null;
	if (isset($_POST['store_id'])) {
		$storeId = (int) $_POST['store_id'];
		$userId = -1;
		$page = 1;
		if (isset($_POST['user_id'])) {
			$userId = (int) $_POST['user_id'];
		}
		if (isset($_POST['page'])) {
			$page = (int) $_POST['page'];
		}
		if ($page < 1) {
			$page = 1;
		}
		$storeDataSource = new StoreDataSource(DbConnection::getConnection());
		$response = $storeDataSource->getStoreComments($userId, $storeId, $page);
	} else {
		$response = new Response(678, new ApiError(678, "Thiếu store id."));
	}
	header($response->code);
	echo json_encode($response->value);
?>
