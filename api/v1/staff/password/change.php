<?php
	require_once('../../../../connect/DbConnection.php');
	require_once('../../../../datasource/staff/StaffDataSource.php');
	require_once('../../../../model/response/ApiError.php');
	require_once('../../../../model/response/Response.php');
	require_once('../../../../util/check/token/CheckToken.php');
	$response = null;
	if (isset($_POST['staff_id']) && isset($_POST['old_pass']) && isset($_POST['new_pass'])) {
		$staffId = $_POST['staff_id'];
		$oldPass = $_POST['old_pass'];
		$newPass = $_POST['new_pass'];
		$CheckToken = CheckToken::checkStaffToken($staffId);
		switch ($CheckToken) {
			case 0:
				$response = Response::getAuthorizationError();
				break;
		
			case 1:
				$staffDataSource = new StaffDataSource(DbConnection::getConnection());
				$response = $staffDataSource->changePassword($staffId, $oldPass, $newPass);
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
