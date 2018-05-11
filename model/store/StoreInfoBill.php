<?php

	require_once('/storage/ssd3/122/4702122/public_html/model/Address.php');	

	class StoreInfoBill {

		function __construct($id, $name, $address, $lat, $lng, $image) {
			$this->id = $id;
			$this->name = $name;
			$this->address = new Address($address, $lat, $lng);
			$this->image = $image;
		}
	}
?>
