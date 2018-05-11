<?php
	class OrderedDrink {

		function __construct($row) {
			$this->id = $row['id'];
			$this->drink_id = $row['drink_id'];
			$this->drink_name = $row['drink_name'];
			$this->drink_price = $row['drink_price'];
			$this->drink_image = $row['drink_image'];
			$this->count = $row['drink_count'];
			$this->note = $row['note'];
			$this->drink_options = json_decode($row['drink_option']);
		}
	}
?>
