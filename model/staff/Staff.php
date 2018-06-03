<?php
	
	class Staff {

		function __construct ($row) {
			$this->token = $row['token'];
			$this->id = $row['id'];
			$this->account = $row['account'];
			$this->full_name = $row['full_name'];
			$this->email = $row['email'];
			$this->phone_number = $row['phone_number'];
			$this->birth_day = $row['birth_day'];
			$this->address = $row['address'];
			$this->personal_id = $row['personal_id'];
			$this->token = $row['token'];
			$this->status = $row['status'];
		}
	}
?>
