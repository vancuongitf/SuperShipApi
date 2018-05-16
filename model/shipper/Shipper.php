<?php
	
	class Shipper {
		function __construct($row) {
			$this->id = $row['shipper_id'];
			$this->full_name = $row['full_name'];
			$this->birth_day = $row['birth_day'];
			$this->address = $row['address'];
			$this->phone_number = $row['phone_number'];
			$this->deposit = $row['deposit'];
			$this->personal_id = $row['personal_id'];
			$this->email = $row['email'];
			$this->status = $row['status'];
		}
	}
?>
