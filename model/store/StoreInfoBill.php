<?php
	$path = getcwd();
	$paths = explode("public_html", $path);
	$basePath = $paths[0];
	require_once($basePath . 'public_html/model/Address.php');


	class StoreInfoBill {

		function __construct($id, $name, $address, $lat, $lng, $image) {
			$this->id = $id;
			$this->name = $name;
			$this->address = new Address($address, $lat, $lng);
			$this->image = $image;
		}
	}
?>
