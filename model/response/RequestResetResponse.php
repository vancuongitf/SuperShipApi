<?php
	class RequestResetResponse {

		function __construct($userId, $userName) {
			$this->userId = $userId;
			$this->userName = $userName;
		}
	}
?>
