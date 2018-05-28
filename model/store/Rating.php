<?php
	class Rating {
		function __construct($value, $count, $isSelected) {
			$this->value = $value;
			$this->rating_count = $count;
			$this->is_selected = $isSelected;
		}
	}
?>
