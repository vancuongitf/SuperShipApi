<?php
	
	class ShipperInfo {
		function __construct($row) {
			$this->id = $row['shipper_id'];
			$this->full_name = $row['full_name'];
			$this->address = $row['address'];
			$this->phone_number = $row['phone_number'];
			$this->email = $row['email'];
			$this->status = $row['status'];
		}
	}
?>
