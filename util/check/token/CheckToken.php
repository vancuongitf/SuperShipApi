<?php
	require_once('../../../../connect/DbConnection.php');

	class CheckToken {
		static function checkUserToken($token, $id){
			$mysql = DBConnection::getConnection();
			if ($mysql) {
				$stmt = $mysql->prepare("SELECT user_id FROM user WHERE user_id = ? AND user_token = ?");
				$stmt->bind_param('i', $id);
				$stmt->bind_param('s', $token);
				$stmt->execute();
				$result = $stmt->get_result();
				if (mysqli_num_rows($result) == 1) {
					return 1;
				} else {
					return 0;
				}
			} else {
				return -1;
			}
		} 
	}	
?>
