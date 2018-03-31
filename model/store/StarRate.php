<?php
	class StarRate {
		var $rate;
		var $rate_count;

		function __construct($row) {
			$this->rate = floatval($row['rate_value']);
			$this->rate_count = (int)$row['rate_count'];
		}
	}
?>