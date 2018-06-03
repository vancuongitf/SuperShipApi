<?php
	$path = getcwd();
	$paths = explode("public_html", $path);
	$basePath = $paths[0];
	require_once($basePath . 'public_html/model/user/User.php');
	require_once($basePath . 'public_html/model/response/Response.php');
	require_once($basePath . 'public_html/model/response/ApiError.php');
	require_once($basePath . 'public_html/model/response/RequestResetResponse.php');
	require_once($basePath . 'public_html/model/response/MessageResponse.php');
	require_once($basePath . 'public_html/model/user/Token.php');
	require_once($basePath . 'public_html/model/staff/Staff.php');
	require_once($basePath . 'public_html/model/store/ExpressBill.php');
	require_once($basePath . 'public_html/model/response/BillListResponse.php');
	require_once($basePath . 'public_html/model/shipper/ShipperInfo.php');
	require_once($basePath . 'public_html/model/shipper/Shipper.php');

	class StaffDataSource {
		var $mysql;

		function __construct($sql) {
			$this->mysql = $sql;
		}

		function login($account, $pass) {
			if($this->mysql) {
				$token = sha1($account . microtime(true));
				$query = "UPDATE staff SET token = '{$token}' WHERE account = '{$account}' AND password = '{$pass}'";
				mysqli_query($this->mysql, $query);
				if (mysqli_affected_rows($this->mysql) == 1) {
					$query = "SELECT status FROM staff WHERE account = '{$account}'";
					$rs = mysqli_query($this->mysql, $query);
					if (mysqli_num_rows($rs) == 1) {
						$row = $rs->fetch_assoc();
						$status = $row['status'];
						switch ($status) {
							case '0':
								return new Response(678, new ApiError(678 ,"Tài khoản chưa được kích hoạt. Vui lòng liên hệ admin để được kích hoạt."));
							
							case '1':
								return $this->getStaffInfo($token); 														

							case '2':
								return new Response(678, new ApiError(678 ,"Tài khoản đã bị khoá. Vui lòng liên hệ tổng đài để được hỗ trợ."));

							default:
								return new Response(678, new ApiError(678 ,"Xãy ra lỗi! Vui lòng thử lại sau."));
						}
					} else {
						return new Response(678, new ApiError(678 ,"Xãy ra lỗi! Vui lòng thử lại sau."));
					}
				} else {
					return new Response(678, new ApiError(678 ,$query));
				}
			} else {
					return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		function getStaffInfo($token) {
			if ($token == null || $token == "") {
				return new Response(401, ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));
			}
			if ($this->mysql) {
				$query = "SELECT * FROM staff WHERE token = '{$token}'";
				$rs = mysqli_query($this->mysql, $query);
				$numRows = mysqli_num_rows($rs);
				switch ($numRows) {
					case 0:
						return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));

					case 1:
						$row = $rs->fetch_assoc();
						$userInfo = new Staff($row);
						return new Response(200, $userInfo);
					default:
						$query = "UPDATE staff SET token = '' WHERE token = $token";
						mysqli_query($this->mysql, $query);
						return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));
				}
			} else {
					return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		function getBills($token, $status, $id, $page) {
			if ($this->mysql) {
				$staffId = $this->getStaffIdFromToken($token);
				switch($staffId) {

					case -2:
						return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của serve."));

					case -1:
						return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));

					default:
						$ignore = ($page - 1) * 20;
						$query = "SELECT bill.bill_id, bill.bill_status, billPrice(bill.bill_id) as bill_price, store.store_name, store.store_image FROM bill INNER JOIN store ON bill.bill_store_id = store.store_id WHERE bill.bill_status = {$status} AND bill.bill_id LIKE '%{$id}%' ORDER BY bill.bill_time DESC LIMIT {$ignore}, 20";
						$passResultQuery = "SELECT bill.bill_id from bill WHERE bill.bill_status = {$status} AND bill.bill_id LIKE '%{$id}%'";
						$bills = array();
						$nexPageFlag = false;
						$result = mysqli_query($this->mysql, $query);
						if (mysqli_num_rows($result) > 0) {
							while ($row = $result->fetch_assoc()) {
								array_push($bills, new ExpressBill($row));
							}
							if (mysqli_num_rows(mysqli_query($this->mysql, $passResultQuery)) > $page * 20) {
								$nexPageFlag = true;
							}
						}
						return new Response(200, new BillListResponse($nexPageFlag, $bills));
				}
			} else {
				return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của serve."));
			}
		}

		function getBillInfo($token, $id) {
			if ($this->mysql) {
				$staffId = $this->getStaffIdFromToken($token);
				switch($staffId) {

					case -2:
						return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của serve."));

					case -1:
						return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));

					default:
						$query = "SELECT bill.*, billPrice($id) AS price ,store.store_id, store.store_name, store.store_address, store.store_lat, store.store_lng, store.store_image FROM store INNER JOIN bill ON bill.bill_store_id = store.store_id WHERE bill.bill_id = {$id}";
						$result = mysqli_query($this->mysql, $query);
						if (mysqli_num_rows($result) == 1) {
							$bill = new Bill($result->fetch_assoc());
							$query = "SELECT * FROM bill_drink WHERE bill_id = {$id};";
							$orderedDrinks = array();
							$rs = mysqli_query($this->mysql, $query);
							if(mysqli_num_rows($rs) > 0) {
								while ($row = $rs->fetch_assoc()) {
									array_push($orderedDrinks, new OrderedDrink($row));
								}
							}
							$bill->drinks = $orderedDrinks;
							$bill->request_shipper = 1;
							return new Response(200, $bill);
						} else {
							return new Response(678, new ApiError(678, "Xãy ra lỗi! Vui lòng thử lại sau."));
						}
				}
			} else {
				return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của serve."));
			}
		}

		function checkedBill($token, $id, $status) {
			if ($this->mysql) {
				$staffId = $this->getStaffIdFromToken($token);
				switch($staffId) {

					case -2:
						return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của serve."));

					case -1:
						return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));

					default:
						$query = "UPDATE bill SET bill_status = {$status}, bill_staff_checked_id = {$staffId} WHERE bill_id = {$id} AND bill_status = 0";
						mysqli_query($this->mysql, $query);
						if (mysqli_affected_rows($this->mysql) == 1) {
							if($status == -1) {
								return new Response(200, new MessageResponse("Hủy đơn hàng thành công."));
							} else {
								return new Response(200, new MessageResponse("Xác nhận đơn hàng thành công."));
							}
						} else {
							return new Response(678, new ApiError(678, "Xãy ra lỗi! Vui lòng thử lại sau."));
						}
				}
			} else {
				return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của serve."));
			}
		}

		function getShipperList($token, $str, $page, $status) {
			if ($this->mysql) {
				$staffId = $this->getStaffIdFromToken($token);
				switch($staffId) {

					case -2:
						return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của serve."));

					case -1:
						return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));

					default:
						$ignore = ($page - 1) * 20;
						$query = "SELECT shipper_id, full_name, address, phone_number, email, status FROM shipper WHERE status = {$status} AND ((shipper.shipper_id like '%{$str}%') OR (shipper.full_name like '%{$str}%') OR (shipper.account like '%{$str}%') OR (shipper.email like '%{$str}%')) LIMIT {$ignore}, 20";
						$passQuery = "SELECT shipper_id from shipper WHERE status = {$status} AND ((shipper.shipper_id like '%{$str}%') OR (shipper.full_name like '%{$str}%') OR (shipper.account like '%{$str}%') OR (shipper.email like '%{$str}%'))";
						$result = mysqli_query($this->mysql, $query);
						$shippers = array();
						$nextPageFlag = false;
						if (mysqli_num_rows($result) > 0) {
							while ($row = $result->fetch_assoc()) {
								array_push($shippers, new ShipperInfo($row));
							}
							$passResult = mysqli_query($this->mysql, $passQuery);
							if (mysqli_num_rows($passResult) > $page * 20) {
								$nextPageFlag = true;
							}
						} 
						return new Response(200, new BillListResponse($nextPageFlag, $shippers));
				}
			} else {
				return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của serve."));
			}
		}

		function getShiperInfo($token, $shipperId) {
			$staffId = $this->getStaffIdFromToken($token);
			switch ($shipperId) {
				case -2:
					return new Response(678, new ApiError(678, "Không thể kết nối tới cơ sở dữ liệu của server."));	

				case -1:
					return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));			
				default:
					$query = "SELECT * FROM shipper WHERE shipper_id = {$shipperId}";
					$result = mysqli_query($this->mysql, $query);
					if (mysqli_num_rows($result) == 1) {
						$row = $result->fetch_assoc();
						return new Response(200, new Shipper($row));
					} else {
						return new Response(678, new ApiError(678, "Xãy ra lỗi. Vui lòng thử lại sau."));	
					}
			}
		}

		function changeUserStatus($token, $id, $status, $isShipper) {
			if ($this->mysql) {
				$staffId = $this->getStaffIdFromToken($token);
				switch($staffId) {

					case -2:
						return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của serve."));

					case -1:
						return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));

					default:
						if ($isShipper) {
							$query = "UPDATE shipper SET status = {$status}, active_staff = {$staffId} WHERE shipper_id = {$id}";
							mysqli_query($this->mysql, $query);
							if (mysqli_affected_rows($this->mysql) == 1) {
								return new Response (200, new MessageResponse("Cập nhật trạng thái shipper thành công."));
							} else {
								return new Response (678, new ApiError(678, "Xãy ra lỗi. Vui lòng thử lại sau."));
							}
						}
				}
			} else {
				return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của serve."));
			}
		}

		function requestResetPass($email) {
			if ($this->mysql) {
				$query = "SELECT id, account FROM staff WHERE email = '{$email}';";
				$result = mysqli_query($this->mysql, $query);
				if (mysqli_num_rows($result) == 1) {
					$row = $result->fetch_assoc();
					$id = $row['id'];
					$userName = $row['account'];
					$otp = rand(100000, 999999);
					$otp_time = time();
					$deleteQuery = "DELETE FROM otp WHERE staff_id = {$id}";
					mysqli_query($this->mysql, $deleteQuery);
					$query = "INSERT INTO otp (staff_id, otp_code, otp_time) VALUES ($id, $otp, $otp_time)";
					mysqli_query($this->mysql, $query);
					if (mysqli_affected_rows($this->mysql) == 1) {
						if (@mail($email,"SUPER SHIP - ĐẶT LẠI MẬT KHẨU","Mã OTP của quý khách là: {$otp}. Mã chỉ có hiệu lực trong vòng 5 phút. Xin chân thành cảm ơn.")) {
							return new Response(200, new RequestResetResponse($id, $userName));
						} else {
							return Response::getNormalError();
						}
					} else {
						return Response::getNormalError();
					}
				} else {
					return Response::getNormalErrorWithMessage("Email chưa đăng ký trên hệ thống.");
				}
			} else {
					return Response::getSQLConnectionError();
			}
		}

		function resetPassword($staffId, $pass, $otp) {
			$checkTime = time() - 300;
			if ($this->mysql) {
				$query = "DELETE FROM otp WHERE staff_id = {$staffId} AND otp_code = {$otp} AND otp_time > {$checkTime};";
				mysqli_query($this->mysql, $query);
				if (mysqli_affected_rows($this->mysql) > 0) {
					$token = sha1($staffId . microtime(true));
					$query = "UPDATE staff SET password = '{$pass}', token = '{$token}' WHERE id = $staffId;";
					mysqli_query($this->mysql, $query);
					if (mysqli_affected_rows($this->mysql) == 1) {
						$query = "DELETE FROM otp WHERE staff_id = {$staffId}";
						mysqli_query($this->mysql, $query);
						return new Response(200, new Token($token));
					} else {
						return Response::getNormalError();
					}
				} else {
					return Response::getNormalErrorWithMessage("Mã OTP không chính xác hoặc đã hết hạn.");
				}
			} else {
					return Response::getSQLConnectionError();
			}
		}

		function changePassword($staffId, $oldPass, $newPass) {
			if ($this->mysql) {
				$query = "UPDATE staff SET password = '{$newPass}' WHERE id = {$staffId} AND password = '{$oldPass}'";
				mysqli_query($this->mysql, $query);
				if (mysqli_affected_rows($this->mysql) > 0) {
					return Response::getMessageResponseWithMessage("Đổi mật khẩu thành công.");
				} else {
					return Response::getNormalErrorWithMessage("Mật khẩu cũ không chính xác.");
				}
			} else {
					return Response::getSQLConnectionError();
			}
		}
		/**
		* Child function.
		*
		*/

		function getStaffIdFromToken($token) {
			if ($token == null || $token == "") {
				return -1;
			}
			if ($this->mysql) {
				$query = "SELECT id FROM staff WHERE token = '{$token}'";
				$rs = mysqli_query($this->mysql, $query);
				$numRows = mysqli_num_rows($rs);
				switch ($numRows) {
					case 0:
						return -1;
					case 1:
						$row = $rs->fetch_assoc();
						return (int)($row['id']);
					default:
						$query = "UPDATE staff SET token = '' WHERE token = '{$token}'";
						mysqli_query($this->mysql, $query);
						return -1;
				}
			} else {
					return -2;
			}
		}
	}
?>
