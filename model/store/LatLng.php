<?php
	class LatLng {
		var $latitude;
		var $longitude;
		function __construct($lat, $lng) {
			$this->latitude = floatval($lat);
			$this->longitude = floatval($lng);
		} 
	}
?>