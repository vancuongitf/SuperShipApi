<?php
	class SubMenu {
		var $menu_id;
		var $menu_name;
		var $menu_store_id;
		var $drinks;

		function __construct($row) {
			$this->menu_id = (int)$row['menu_id'];
			$this->menu_name = $row['menu_name'];
			$this->menu_store_id = $row['menu_store_id'];
		}
	}
?>