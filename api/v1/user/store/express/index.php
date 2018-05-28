<?php
	require_once('../../../../../connect/DbConnection.php');
	require_once('../../../../../datasource/store/StoreDataSource.php');
	$storeDataSource = null;
	$response = null;
	$page = 1;
	$token = null;
	date_default_timezone_set("Asia/Bangkok");
	if (isset($_GET['token'])) {
		$token = $_GET['token'];
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		}
		if ($page < 1) {
			$page = 1;
		}
		$storeDataSource = new StoreDataSource(DbConnection::getConnection());
		$response = $storeDataSource->getExpressStoreByAccessToken($token, $page);
	} else {
		$response = new Response(678, new ApiError(678, "Thiếu user id."));
	}
	header($response->code);
	echo json_encode($response->value);
?>
