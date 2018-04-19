<?php
	require_once('/storage/ssd3/122/4702122/public_html/model/user/User.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/Response.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/RequestResetResponse.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/MessageResponse.php');
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

		function createUser($user) {
			if ($this->mysql) {
				$query = "INSERT INTO user(user_name, user_password, user_full_name, user_email, user_phone, is_shipper, status) VALUES ('{$user->name}', '{$user->password}', '{$user->full_name}', '{$user->email}', '{$user->phone}', $user->is_shipper, $user->status );";
				mysqli_query($this->mysql, $query);
				if (mysqli_affected_rows($this->mysql) == 1) {
					return new Response(200, "ok");
				} else {
					return new Response(200, "xxx");
				}
			} else {
					return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		function requestResetPass($email) {
			if ($this->mysql) {
				$query = "SELECT user_id, user_name FROM user WHERE user_email = '{$email}';";
				$result = mysqli_query($this->mysql, $query);
				if (mysqli_num_rows($result) == 1) {
					$row = $result->fetch_assoc();
					$id = $row['user_id'];
					$userName = $row['user_name'];
					$otp = rand(100000, 999999);
					$otp_time = time();
					$query = "INSERT INTO otp (user_id, otp_code, otp_time) VALUES ($id, $otp, $otp_time)";
					mysqli_query($this->mysql, $query);
					if (mysqli_affected_rows($this->mysql) == 1) {
						if (@mail($email,"SUPER SHIP - ĐẶT LẠI MẬT KHẨU","Mã OTP của quý khách là: {$otp}. Mã chỉ có hiệu lực trong vòng 5 phút. Xin chân thành cảm ơn.")) {
							return new Response(200, new RequestResetResponse($id, $userName));
						} else {
							return new Response(678, new ApiError(678, "Xãy ra lỗi. Vui lòng thử lại sau!"));
						}
					} else {
						return new Response(678, new ApiError(678, "Xãy ra lỗi. Vui lòng thử lại sau!"));
					}
				} else {
					return new Response(678, new ApiError(678, "Email chưa đăng ký trên hệ thống!"));
				}
			} else {
					return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		function resetPassword($userId, $pass, $otp) {
			$checkTime = time() - 300;
			if ($this->mysql) {
				$query = "DELETE FROM otp WHERE user_id = $userId AND otp_code = $otp AND otp_time > {$checkTime};";
				mysqli_query($this->mysql, $query);
				if (mysqli_affected_rows($this->mysql) > 0) {
					$token = sha1($userId . microtime(true));
					$query = "UPDATE user SET user_password = '{$pass}', user_token = '{$token}' WHERE user_id = $userId;";
					mysqli_query($this->mysql, $query);
					if (mysqli_affected_rows($this->mysql) == 1) {
						$query = "DELETE FROM otp WHERE user_id = $userId";
						mysqli_query($this->mysql, $query);
						return new Response(200, new Token($token));
					} else {
						return new Response(678, new ApiError(678, "Xãy ra lỗi. Vui lòng thử lại sau!"));
					}
				} else {
					return new Response(678, new ApiError(678, $query));
				}
			} else {
					return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}
	}
?>