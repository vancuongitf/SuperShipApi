<?php
	
	require_once('/storage/ssd3/122/4702122/public_html/model/store/LatLng.php');	
	
	class Location {
		function __construct($row) {
			$this->lat_lng = new LatLng($row['lat'], $row['lng']);
			$this->last_modify = $row['last_modify'];
		}
	}
?>
