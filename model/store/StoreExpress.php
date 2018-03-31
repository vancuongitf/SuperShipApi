<?php
	require_once('/storage/ssd3/122/4702122/public_html/model/store/OpenTime.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/store/StarRate.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/store/LatLng.php');
	class StoreExpress {
		var $store_id;
		var $store_name;
		var $store_address;
		var $store_lat_lng;
		var $store_distance;
		var $store_rate;
		var $store_image;

		function __construct($row) {
			$this->store_id = $row['store_id'];
			$this->store_address = $row['store_address'];
			if (isset($row['store_distance'])) {
				$this->store_distance = $row['store_distance'];				
			}
			$this->store_image = $row['store_image'];
			$this->store_name = $row['store_name'];
			$this->store_open_time = new OpenTime($row['store_open_day'], $row['store_open_hour'], $row['store_close_hour']);
			$this->store_rate = new StarRate($row);
			$this->store_lat_lng = new LatLng($row['store_lat'], $row['store_lng']);
		}
	}
?>
