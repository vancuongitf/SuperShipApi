<?php
	class OpenTime {
		var $open_days;
		var $open;
		var $close;
		function __construct($sourceDay, $openTime, $closeTime) {
			$this->open_days = json_decode($sourceDay);
			if ($openTime != null) {
				$this->open = (int)$openTime;
			}
			if ($closeTime!=null) {
				$this->close = (int)$closeTime;
			}
		} 
	}
?>