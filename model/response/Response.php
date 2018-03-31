<?php
	class Response {
		function __construct($code, $data) {
			if ($code == 200) {
				$this->code = "HTTP/1.1 " . $code . " OK";				
			} else {
				$this->code = "HTTP/1.1 " . $code . " API ERROR";				
			}
			$this->value = $data;
		}
	}
?>