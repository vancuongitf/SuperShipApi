<?php
	class BillListResponse {
		var $next_page_flag;
		var $bill_list;
		function __construct($next_page_flag, $bills) {
			$this->next_page_flag = $next_page_flag;
			$this->bill_list = $bills;
		}
	}
?>
