<?php
	require_once('/storage/ssd3/122/4702122/public_html/model/user/User.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/Response.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/user/Token.php');

	class UserDataSource {
		var $mysql;

		function __construct($sql) {
			$this->mysql = $sql;
		}

		function login($user, $password) {
			if($this->mysql) {
				$token = sha1($user . microtime(true));
				$query = "UPDATE user SET user_token = '{$token}' WHERE user_name = '{$user}' AND user_password = '{$password}'";
				mysqli_query($this->mysql, $query);
				if (mysqli_affected_rows($this->mysql) == 1) {
						return new Response(200, new Token($token)); 
				} else {
					return new Response(678, new ApiError(678 ,"Mật khẩu hoặc tài khoản không đúng."));
				}
			} else {
					return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}
	}
?>