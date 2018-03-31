<?php
	require_once('../../../connect/DbConnection.php');
	require_once('../../../datasource/store/StoreDataSource.php');
	$storeDataSource = null;
	$response = null;
	$advance_param = 1;
	$page = 1;
	if (isset($_GET['advance_param'])){
		$advance_param = $_GET['advance_param'];
	}
	if (isset($_GET['page'])) {
		$page = $_GET['page'];
	}
	if ($page < 1) {
		$page = 1;
	}
	// Create connection
	$mysql = DbConnection::getConnection();
	$storeDataSource = new StoreDataSource($mysql);
	if (isset($_GET['lat']) && isset($_GET['lng'])) {
		$lat = $_GET['lat'];
		$lng = $_GET['lng'];
		$response = $storeDataSource->getExpressesStore($advance_param, $page, $lat, $lng);
	}else{
		$response = $storeDataSource->getExpressesStore($advance_param, $page, null, null);
	}
	header($response->code);
	echo json_encode($response->value);
?>