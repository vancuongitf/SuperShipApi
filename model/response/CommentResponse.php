<?php
	class CommentResponse {
		function __construct($nextPageFlag, $ratings, $comments) {
			$this->next_page_flag = $nextPageFlag;
			$this->ratings = $ratings;
			$this->comments = $comments;
		}
	}
?>
