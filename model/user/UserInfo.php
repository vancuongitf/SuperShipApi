<?php
	/**
	* 
	*/
	class UserInfo {
		
		function __construct($row) {
			$this->token = $row['user_token'];
			$this->user_id = $row['user_id'];
			$this->user_name = $row['user_name'];
			$this->full_name = $row['user_full_name'];
			$this->email = $row['user_email'];
			$this->phone = $row['user_phone'];
			$this->bill_addresses = array();
		}
	}
?>
