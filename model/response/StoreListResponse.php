<?php
	class StoreListResponse {
		var $next_page_flag;
		var $store_list;
		function __construct($next_page_flag, $store_list) {
			$this->next_page_flag = $next_page_flag;
			$this->store_list = $store_list;
		}
	}
?>