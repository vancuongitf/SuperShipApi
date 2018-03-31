<?php
	class Drink {
		var $drink_id;
		var $drink_menu_id;
		var $drink_name;
		var $drink_price;
		var $drink_image;
		var $drink_options;

		function __construct($row) {
			$this->drink_id = (int)$row['drink_id'];
			$drink_menu_id = $row['drink_menu_id'];
			if ($drink_menu_id) {
				$this->drink_menu_id = (int)$drink_menu_id;
			} else {
				$this->drink_menu_id = NULL;				
			}
			$this->drink_name = $row['drink_name'];
			$this->drink_price = (int)$row['drink_price'];
			$this->drink_image = $row['drink_image'];
		}
	}
?>