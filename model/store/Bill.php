<?php

	require_once('/storage/ssd3/122/4702122/public_html/model/Address.php');	
	require_once('/storage/ssd3/122/4702122/public_html/model/store/StoreInfoBill.php');	

	class Bill {

		function __construct($row) {
			$this->bill_id = $row['bill_id'];
			$this->bill_user_id = $row['bill_user_id'];
			$this->bill_user_name = $row['bill_user_name'];
			$this->bill_user_phone = $row['bill_user_phone'];
			$this->bill_address = new Address($row['bill_address'], $row['bill_lat'], $row['bill_lng']);
			$this->bill_ship_road = $row['bill_ship_road'];
			$this->bill_shipper_id = $row['bill_shipper_id'];
			$this->bill_time = $row['bill_time'];
			$this->bill_price = $row['price'];
			$this->bill_ship_price = $row['bill_ship_price'];
			$this->bill_status = $row['bill_status'];
			$this->online_payment = $row['online_payment'];
			$this->confirm_code = $row['confirm_code'];
			$this->bill_complete_time = $row['bill_complete_time'];
			$this->store = new StoreInfoBill($row['store_id'], $row['store_name'], $row['store_address'], $row['store_lat'], $row['store_lng'], $row['store_image']);
		}
	}
?>
