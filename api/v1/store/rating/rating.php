<?php
	require_once('../../../../connect/DbConnection.php');
	require_once('../../../../datasource/store/StoreDataSource.php');
	require_once('../../../../model/response/ApiError.php');
	require_once('../../../../model/response/Response.php');
	require_once('../../../../util/check/token/CheckToken.php');
	$response = null;
	if (isset($_POST['user_id']) && isset($_POST['store_id']) && isset($_POST['rate'])) {
		$userId = $_POST['user_id'];
		$storeId = $_POST['store_id'];
		$rate = (int) $_POST['rate'];
		if ($rate > 5 || $rate < 1) {
			$response = Response::getNormalErrorWithMessage("Giá trị đánh giá không hợp lện.");
		} else {
			$CheckToken = CheckToken::checkUserToken($userId);
			switch ($CheckToken) {
				case 0:
					$response = Response::getAuthorizationError();
					break;
			
				case 1:
					$storeDataSource = new StoreDataSource(DbConnection::getConnection());
					$response = $storeDataSource->starRating($userId, $storeId, $rate);
					break;
				default:
					$response = Response::getSQLConnectionError();
					break;
		}
		}
	} else {
		$response = Response::getMissingDataError();
	}
	header($response->code);
	echo json_encode($response->value);
?>
