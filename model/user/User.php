<?php
	class User {
		function __construct($row) {
			$this->user_id = $row['user_id'];
			$this->user_name = $row['user_name'];
			$this->user_full_name = $row['user_full_name'];
			$this->user_email = $row['user_email'];
			$this->user_phone = $row['user_phone'];
			$this->token = $row['user_token'];
		}
	}
?>