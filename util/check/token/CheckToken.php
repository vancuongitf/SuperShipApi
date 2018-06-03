<?php
	const ACCESS_TOKEN = "HTTP_ACCESS_TOKEN";
	$path = getcwd();
	$paths = explode("public_html", $path);
	$basePath = $paths[0];
	require_once($basePath . 'public_html/connect/DbConnection.php');

	class CheckToken {
		static function checkUserToken($id){
			$mysql = DBConnection::getConnection();
			if ($mysql) {
				if (isset($_SERVER[ACCESS_TOKEN])) {
					$token = $_SERVER[ACCESS_TOKEN];
					$stmt = $mysql->prepare("SELECT user_id FROM user WHERE user_id = ? AND user_token = ?");
					$stmt->bind_param('is', $id, $token);
					$stmt->execute();
					$result = $stmt->get_result();
					if (mysqli_num_rows($result) == 1) {
						return 1;
					} else {
						return 0;
					}	
				} else {
					return 0;
				}
			} else {
				return -1;
			}
		} 

		static function checkShipperToken($id){
			$mysql = DBConnection::getConnection();
			if ($mysql) {
				if (isset($_SERVER[ACCESS_TOKEN])) {
					$token = $_SERVER[ACCESS_TOKEN];
					$stmt = $mysql->prepare("SELECT shipper_id FROM shipper WHERE shipper_id = ? AND token = ?");
					$stmt->bind_param('is', $id, $token);
					$stmt->execute();
					$result = $stmt->get_result();
					if (mysqli_num_rows($result) == 1) {
						return 1;
					} else {
						return 0;
					}	
				} else {
					return 0;
				}
			} else {
				return -1;
			}
		}

		static function checkStaffToken($id){
			$mysql = DBConnection::getConnection();
			if ($mysql) {
				if (isset($_SERVER[ACCESS_TOKEN])) {
					$token = $_SERVER[ACCESS_TOKEN];
					$stmt = $mysql->prepare("SELECT id FROM staff WHERE id = ? AND token = ?");
					$stmt->bind_param('is', $id, $token);
					$stmt->execute();
					$result = $stmt->get_result();
					if (mysqli_num_rows($result) == 1) {
						return 1;
					} else {
						return 0;
					}	
				} else {
					return 0;
				}
			} else {
				return -1;
			}
		}
	}	
?>
