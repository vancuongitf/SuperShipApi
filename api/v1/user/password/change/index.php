<?php
	require_once('/storage/ssd3/122/4702122/public_html/connect/DbConnection.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/Response.php');
	require_once('/storage/ssd3/122/4702122/public_html/datasource/user/UserDataSource.php');
	$response = null;
	if (isset($_POST['token']) && isset($_POST['pass']) && isset($_POST['old_pass'])) {
		$token = $_POST['token'];
		$pass = $_POST['pass'];
		$oldPass = $_POST['old_pass'];
		$userDataSource = new UserDataSource(DbConnection::getConnection());
		$response = $userDataSource->changePassword($token, $oldPass, $pass);
	} else {
		$response = new Response(678, new ApiError(678, "Thiếu dữ liệu. Vui lòng thử lại sau."));
	}
	header($response->code);
	echo json_encode($response->value);
?>
