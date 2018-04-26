<?php
	
	class UserInfo {
		
		function __construct($row)
		{
			$this->user_id = $row['user_id'];
			$this->full_name = $row['user_full_name'];
			$this->phone_number = $row['phone'];
			$this->email = $row['email'];
			$this->bill_addresses = array();
		}
	}
?>
