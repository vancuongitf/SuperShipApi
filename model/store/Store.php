<?php
	require_once('/storage/ssd3/122/4702122/public_html/model/store/OpenTime.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/store/StarRate.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/store/LatLng.php');
	class Store {
		var $store_id;
		var $store_user_id;
		var $store_name;
		var $store_address;	
		var $store_lat_lng;
		var $store_phone;
		var $store_email;
		var $store_image;	
		var $store_open_time;
		var $store_rate;
		var $menu;
		var $options;

		function __construct($row) {
			$this->store_id = (int)$row['store_id'];
			$this->store_user_id = (int)$row['store_user_id'];
			$this->store_name = $row['store_name'];
			$this->store_address = $row['store_address'];
			$this->store_lat_lng = new LatLng($row['store_lat'], $row['store_lng']);
			$this->store_phone = $row['store_phone'];
			$this->store_email = $row['store_email'];
			$this->store_image = $row['store_image'];	
			$this->store_open_time = new OpenTime($row['store_open_day'], $row['store_open_hour'], $row['store_close_hour']);
			$this->store_rate = new StarRate($row);
		}	
	}	
?>