<?php
	require_once('../../../../model/response/ApiError.php');
	require_once('../../../../model/response/Response.php');
	require_once('../../../../connect/DbConnection.php');
	require_once('../../../../datasource/store/StoreDataSource.php');
	$response = null;
	if (isset($_POST['id']) && isset($_POST['token'])) {
		$id = $_POST['id'];
		$token = $_POST['token'];
		$storeDataSource = new StoreDataSource(DbConnection::getConnection());
		$response = $storeDataSource->deleteDrink($token, $id);
	} else {
		$response = new Response(678, new ApiError(678, "Thiếu dữ liệu."));
	}
	header($response->code);
	echo json_encode($response->value);
?>
