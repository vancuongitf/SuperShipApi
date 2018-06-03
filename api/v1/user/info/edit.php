<?php
	require_once('../../../../connect/DbConnection.php');
	require_once('../../../../datasource/user/UserDataSource.php');
	require_once('../../../../model/response/ApiError.php');
	require_once('../../../../model/response/Response.php');
	require_once('../../../../util/check/token/CheckToken.php');
	$response = null;
	if (isset($_POST['user_id']) && isset($_POST['name']) && isset($_POST['phone'])) {
		$userId = $_POST['user_id'];
		$name = $_POST['name'];
		$phone = $_POST['phone'];
		$CheckToken = CheckToken::checkUserToken($userId);
		switch ($CheckToken) {
			case 0:
				$response = Response::getAuthorizationError();
				break;
			
			case 1:
				$userDataSource = new UserDataSource(DbConnection::getConnection());
				$response = $userDataSource->editInfo($userId, $name, $phone);
				break;
			default:
				$response = Response::getSQLConnectionError();
				break;
		}
	} else {
		$response = Response::getMissingDataError();
	}
	header($response->code);
	echo json_encode($response->value);
?>
