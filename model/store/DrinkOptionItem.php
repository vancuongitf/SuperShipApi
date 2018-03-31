<?php
	class DrinkOptionItem {
		var $drink_option_item_id;
		var $drink_option_id;
		var $drink_option_item_name;
		var $drink_option_item_price;

		function __construct($row) {
			$this->drink_option_item_id = (int)$row['drink_option_item_id'];
			$this->drink_option_id = (int)$row['drink_option_id'];
			$this->drink_option_item_name = $row['drink_option_item_name'];
			$this->drink_option_item_price = (int)$row['drink_option_item_price'];
		}
	}
?>