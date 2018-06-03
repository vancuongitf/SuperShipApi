<?php
	require_once('../../../../connect/DbConnection.php');
	require_once('../../../../datasource/shipper/ShipperDataSource.php');
	require_once('../../../../model/response/ApiError.php');
	require_once('../../../../model/response/Response.php');
	require_once('../../../../util/check/token/CheckToken.php');
	$response = null;
	if (isset($_POST['shipper_id']) && isset($_POST['phone'])) {
		$shipperId = $_POST['shipper_id'];
		$phone = $_POST['phone'];
		$CheckToken = CheckToken::checkShipperToken($shipperId);
		switch ($CheckToken) {
			case 0:
				$response = Response::getAuthorizationError();
				break;
		
			case 1:
				$shipperDataSource = new ShipperDataSource(DbConnection::getConnection());
				$response = $shipperDataSource->changeShipperInfo($shipperId, $phone);
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
