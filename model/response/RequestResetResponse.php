<?php
	class RequestResetResponse {

		function __construct($userId, $userName) {
			$this->user_id = $userId;
			$this->user_name = $userName;
		}
	}
?>
