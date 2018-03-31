<?php
	require_once('../../../connect/DbConnection.php');
	require_once('../../../datasource/store/StoreDataSource.php');
	$storeDataSource = null;
	$response = null;
	$query = '';
	$page = 1;
	if (isset($_GET['query'])){
		$query = $_GET['query'];
	}
	if (isset($_GET['page'])) {
		$page = $_GET['page'];
		if ($page < 1) {
			$page = 1;
		}	
	}
	$mysql = DbConnection::getConnection();
	$storeDataSource = new StoreDataSource($mysql);
	if (isset($_GET['lat']) && isset($_GET['lng'])) {
		$lat = $_GET['lat'];
		$lng = $_GET['lng'];
		$response = $storeDataSource->search($query, $page, $lat, $lng);
	}else{
		$response = $storeDataSource->search($query, $page, null, null);
	}
	header($response->code);
	echo json_encode($response->value);
?>