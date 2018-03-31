<?php
	class ApiError {
		function __construct($code, $message) {
			$this->code = $code;
			$this->message = $message;
		}
	}
?>