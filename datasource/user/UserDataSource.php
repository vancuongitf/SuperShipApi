<?php
	require_once('/storage/ssd3/122/4702122/public_html/model/user/User.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/Response.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/RequestResetResponse.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/MessageResponse.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/user/Token.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/user/UserInfo.php');

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
					$query = "SELECT status FROM user WHERE user_name = '{$user}'";
					$rs = mysqli_query($this->mysql, $query);
					if (mysqli_num_rows($rs) == 1) {
						$row = $rs->fetch_assoc();
						$status = $row['status'];
						switch ($status) {
							case '0':
								return new Response(678, new ApiError(678 ,"Tài khoản chưa được kích hoạt. Vui lòng đăng nhập vào email để kích hoạt và tiếp tục."));
								break;
							
							case '1':
								return new Response(200, new Token($token)); 														
								break;

							case '2':
								return new Response(678, new ApiError(678 ,"Tài khoản đã bị khoá. Vui lòng liên hệ tổng đài để được hỗ trợ."));
								break;

							default:
								return new Response(678, new ApiError(678 ,"Xãy ra lỗi! Vui lòng thử lại sau."));
								break;
						}
					} else {
						return new Response(678, new ApiError(678 ,"Xãy ra lỗi! Vui lòng thử lại sau."));
					}
				} else {
					return new Response(678, new ApiError(678 ,"Mật khẩu hoặc tài khoản không đúng."));
				}
			} else {
					return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		function createUser($user) {
			if ($this->mysql) {
				$check = $this->checkUser($user->name, $user->email);
				if ($check == "ok") {
					$active_key = rand(100000, 999999);
					$query = "INSERT INTO user(user_name, user_password, user_full_name, user_email, user_phone, is_shipper, status, active_key) VALUES ('{$user->name}', '{$user->password}', '{$user->full_name}', '{$user->email}', '{$user->phone}', $user->is_shipper, $user->status, {$active_key});";
					mysqli_query($this->mysql, $query);
					if (mysqli_affected_rows($this->mysql) == 1) {
						if (@mail($user->email,"Kích Hoạt Tài Khoản","Chúc mừng bạn đã đăng ký thành công. Vui lòng click vào link sau để kích hoạt tài khoản: https://vnshipperman.000webhostapp.com/user/active?user={$user->name}&active_key={$active_key}")) {
							return new Response(200, new MessageResponse("Đăng ký thành công. Quý khách vui lòng đăng nhập vào email: {$user->email} để kích hoạt tài khoản."));						
						} else {
							$query = "DELETE FROM user WHERE user_email = '{$user->email}'";
							mysqli_query($this->mysql, $query);
							return new Response(678, "Xãy ra lỗi! Vui lòng thử sau.");
						}
					} else {
						return new Response(678, "Xãy ra lỗi! Vui lòng thử sau.");
					}
				} else {
					return new Response(678, new ApiError(678, $check));
				}
			} else {
				return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		function changePassword($token, $oldpass, $pass){
			if ($this->mysql) {
				$query = "SELECT user_id, user_password FROM user WHERE user_token = '{$token}'";
				$rs = mysqli_query($this->mysql, $query);
				$numRows = mysqli_num_rows($rs);
				switch ($numRows) {
					case 0:
						return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));
						
					case 1:
						$row = $rs->fetch_assoc();
						$userId = (int)($row['user_id']);
						$password = $row['user_password'];
						if ($password == $oldpass ) {
							$query = "UPDATE user SET user_password = '{$pass}' WHERE user_id = {$userId};";
							mysqli_query($this->mysql, $query);
							if (mysqli_affected_rows($this->mysql) == 1) {
								return new Response(200, new MessageResponse("Đổi mật khẩu thành công."));
							} else {
								return new Response(678, new ApiError(678, "Xãy ra lỗi! Vui lòng thử lại sau."));
							}
						} else {
							return new Response(678, new ApiError(678, "Mật khẩu không chính xác."));
						}
					default:
						$query = "UPDATE user SET user_token = '' WHERE user_token = $token";
						mysqli_query($this->mysql, $query);
						return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));
				}
			}else {
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

		function getUserInfo($token) {
			if ($token == null || $token == "") {
				return new Response(401, ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));
			}
			if ($this->mysql) {
				$query = "SELECT * FROM user WHERE user_token = '{$token}'";
				$rs = mysqli_query($this->mysql, $query);
				$numRows = mysqli_num_rows($rs);
				switch ($numRows) {
					case 0:
						return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));

					case 1:
						$row = $rs->fetch_assoc();
						$userInfo = new UserInfo($row);
						return new Response(200, $userInfo);
						break;
					default:
						$query = "UPDATE user SET user_token = '' WHERE user_token = $token";
						mysqli_query($this->mysql, $query);
						return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));
						break;
				}
			} else {
					return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		function activeUser($user, $key) {
			if ($this->mysql) {
				$query = "UPDATE user SET status = 1, active_key = -1 WHERE user_name = '{$user}' AND active_key = {$key}";
				mysqli_query($this->mysql, $query);
				if (mysqli_affected_rows($this->mysql) == 1) {
					return new MessageResponse("Chúc mừng bạn đã kích hoạt tài khoản thành công!");
				} else {
					return new MessageResponse("Xãy ra lỗi! Vui lòng thử lại sau");
				}
			} else {
				return new MessageResponse("Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau.");
			}
		}

		/**
		* Child function.
		*
		*/
		function checkUser($user, $email) {
			$query = "SELECT user_name, user_email FROM user WHERE user_name = '{$user}' OR user_email = '{$email}'";
			$rs = mysqli_query($this->mysql, $query);
			if (mysqli_num_rows($rs) == 1) {
				$row = $rs->fetch_assoc();
				if ($user == $row['user_name']) {
					return "Tài khoản đã đăng ký.";
				} else {
					return "Email đã đăng ký hệ thống.";
				}
			} else {
				return "ok";
			}
		}

		function getUserIdFromToken($token) {
			if ($token == null || $token == "") {
				return -1;
			}
			if ($this->mysql) {
				$query = "SELECT user_id FROM user WHERE user_token = '{$token}'";
				$rs = mysqli_query($this->mysql, $query);
				$numRows = mysqli_num_rows($rs);
				switch ($numRows) {
					case 0:
						return -1;
					case 1:
						$row = $rs->fetch_assoc();
						return (int)($row['user_id']);
					default:
						$query = "UPDATE user SET user_token = '' WHERE user_token = '{$token}'";
						mysqli_query($this->mysql, $query);
						return -1;
				}
			} else {
					return -1;
			}
		}
	}
?>