<?php
	class DrinkOption {
		var $drink_option_id;
		var $drink_option_store_id;
		var $drink_option_name;
		var $drink_option_mutil_choose;
		var $drink_option_items;

		function __construct($row) {
			$this->drink_option_id = (int)$row['drink_option_id'];
			$this->drink_option_store_id = (int)$row['drink_option_store_id'];
			$this->drink_option_name = $row['drink_option_name'];
			$this->drink_option_mutil_choose = $row['drink_option_mutil_choose'];
		}
	}
?>