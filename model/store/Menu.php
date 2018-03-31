<?php
	class Menu {
		var $drinks;
		var $sub_menus;

		function __construct($drinks, $sub_menus) {
			$this->drinks = $drinks;
			$this->sub_menus = $sub_menus;
		}
	}
?>