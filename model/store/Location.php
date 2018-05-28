<?php
	$path = getcwd();
	$paths = explode("public_html", $path);
	$basePath = $paths[0];
	require_once($basePath . 'public_html/model/store/LatLng.php');	
	
	class Location {
		function __construct($row) {
			$this->lat_lng = new LatLng($row['lat'], $row['lng']);
			$this->last_modify = $row['last_modify'];
		}
	}
?>
