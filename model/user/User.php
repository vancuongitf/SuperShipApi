<?php
	class User {
		var $password;
		var $status;
		var $is_shipper;
		function __construct($row) {
			$this->id = $row['user_id'];
			$this->name = $row['user_name'];
			$this->full_name = $row['user_full_name'];
			$this->email = $row['user_email'];
			$this->phone = $row['user_phone'];
			$this->token = $row['user_token'];
		}
	}
?>