<?php
	class Comment {
		function __construct($row) {
			$this->id = $row['id'];
			$this->user_id = $row['user_id'];
			$this->user_full_name = $row['user_full_name'];
			$this->comment = $row['comment'];
			$this->comment_time = $row['comment_time'];
		}
	}
?>
