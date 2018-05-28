<?php
	$path = getcwd();
	$paths = explode("public_html", $path);
	$basePath = $paths[0];
	require_once($basePath . 'public_html/model/response/ApiError.php');
	class Response {
		function __construct($code, $data) {
			if ($code == 200) {
				$this->code = "HTTP/1.1 " . $code . " OK";				
			} else {
				$this->code = "HTTP/1.1 " . $code . " API ERROR";				
			}
			$this->value = $data;
		}

		static function getSQLConnectionError() {
			return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của serve."));
		}

		static function getAuthorizationError() {
			return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));
		}

		static function getNormalError() {
			return new Response(678, new ApiError(678, "Xãy ra lỗi. Vui lòng thử lại sau."));
		}

		static function getNormalErrorWithMessage($message) {
			return new Response(678, new ApiError(678, $message));
		}

		static function getMissingDataError() {
			return new Response(678, new ApiError(678, "Thiếu dữ liệu."));
		}
	}
?>