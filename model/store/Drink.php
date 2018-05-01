<?php
	class Drink {
		var $drink_id;
		var $drink_name;
		var $drink_price;
		var $drink_image;
		var $drink_options;
		var $drink_status;

		function __construct($row) {
			$this->drink_id = (int)$row['drink_id'];
			$this->drink_name = $row['drink_name'];
			$this->drink_price = (int)$row['drink_price'];
			$this->drink_image = $row['drink_image'];
			$this->drink_status = $row['drink_status'];
		}
	}
?>