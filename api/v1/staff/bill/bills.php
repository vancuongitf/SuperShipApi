<?php
	$path = getcwd();
	$paths = explode("public_html", $path);
	$basePath = $paths[0];
	require_once($basePath . 'public_html/connect/DbConnection.php');
	require_once($basePath . 'public_html/model/response/ApiError.php');
	require_once($basePath . 'public_html/datasource/staff/StaffDataSource.php');
	$response = null;
	if (isset($_POST['token']) && isset($_POST['status'])) {
		$token = $_POST['token'];
		$status = (int)$_POST['status'];
		$page = 1;
		$id = '';
		if (isset($_POST['page'])) {
			$page = (int)$_POST['page'];	
			if ($page < 1) {
				$page = 1;
			}							
		}
		if (isset($_POST['id'])) {
			$id = $_POST['id'];
		}
		$staffDataSource = new StaffDataSource(DbConnection::getConnection());
		$response = $staffDataSource->getBills($token, $status, $id, $page);
	} else {
		$response = new Response(678, new ApiError(678, "Thiếu dữ liệu."));
	}
	header($response->code);
	echo json_encode($response->value);
?>
