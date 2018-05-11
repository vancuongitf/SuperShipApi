<?php
	class ExpressBill {
		function __construct($row) {
			$this->bill_id = $row['bill_id'];
			$this->store_name = $row['store_name'];
			$this->store_image = $row['store_image'];
			$this->status = $row['bill_status'];
			$this->price = $row['bill_price'];
		}
	}
?>
