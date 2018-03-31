<?php
	class OpenTime {
		var $open_days;
		var $open;
		var $close;
		function __construct($sourceDay,$openTime, $closeTime) {
			$this->open_days = array();
			if ($sourceDay != null) {
				$openDayString = explode(",", $sourceDay);
				foreach ($openDayString as $day) {
					array_push($this->open_days, (int) $day);
				}	
			}
			if ($openTime != null) {
				$this->open = (int)$openTime;
			}
			if ($closeTime!=null) {
				$this->close = (int)$closeTime;
			}
		} 
	}
?>