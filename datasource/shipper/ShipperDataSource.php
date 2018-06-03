<?php
	$path = getcwd();
	$paths = explode("public_html", $path);
	$basePath = $paths[0];
	require_once($basePath . 'public_html/model/response/Response.php');
	require_once($basePath . 'public_html/model/response/ApiError.php');
	require_once($basePath . 'public_html/model/response/MessageResponse.php');
	require_once($basePath . 'public_html/model/response/RequestResetResponse.php');
	require_once($basePath . 'public_html/model/response/BillListResponse.php');
	require_once($basePath . 'public_html/model/store/ExpressBill.php');
	require_once($basePath . 'public_html/model/user/Token.php');
	require_once($basePath . 'public_html/model/shipper/Shipper.php');

	class ShipperDataSource {

		var $mysql;

		function __construct($sql) {
			$this->mysql = $sql;
		}

		function createShipper($shipper) {
			$check = $this->checkShipper($shipper);
			if ($check == "ok") {
				$query = "INSERT INTO shipper ( `full_name`, `account`, `password`, `birth_day`, `address`, `phone_number`, `personal_id`, `email` ) VALUES ( '{$shipper->full_name}', '{$shipper->account}', '{$shipper->password}', '{$shipper->birth_day}', '{$shipper->address}', '{$shipper->phone_number}', '{$shipper->personal_id}', '{$shipper->email}');";
				mysqli_query($this->mysql, $query);
				if (mysqli_affected_rows($this->mysql) == 1) {
					return new Response(200, new ApiError(678, "Tạo tài khoản thành công. Vui lòng gởi hồ sơ cá nhân đến đại lý gần nhất để đuơc kích hoạt tài khoản."));
				} else {
					return new Response(678, new ApiError(678, "Xãy ra lỗi. Vui lòng thử lại sau."));
				}
			} else {
					return new Response(678, new ApiError(678, $check));
			}
		}

		function login($user, $password) {
			if($this->mysql) {
				$token = sha1($user . microtime(true));
				$query = "UPDATE shipper SET token = '{$token}' WHERE account = '{$user}' AND password = '{$password}'";
				mysqli_query($this->mysql, $query);
				if (mysqli_affected_rows($this->mysql) == 1) {
					$query = "SELECT status FROM shipper WHERE account = '{$user}'";
					$rs = mysqli_query($this->mysql, $query);
					if (mysqli_num_rows($rs) == 1) {
						$row = $rs->fetch_assoc();
						$status = $row['status'];
						switch ($status) {
							case '0':
								return new Response(678, new ApiError(678 ,"Tài khoản chưa được kích hoạt. Vui lòng liên hệ tồng đài để được tư vấn."));
							
							case '1':
								return $this->getShiperInfo($token); 														

							case '2':
								return new Response(678, new ApiError(678 ,"Tài khoản đã bị khoá. Vui lòng liên hệ tổng đài để được hỗ trợ."));

							default:
								return new Response(678, new ApiError(678 ,"Xãy ra lỗi! Vui lòng thử lại sau."));
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

		function getShiperInfo($token) {
			$shipperId = $this->getShipperIdFromToken($token);
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

		function changeShipperInfo($shipperId, $phone) {
			if ($this->mysql) {
				$query = "UPDATE shipper SET phone_number = '{$phone}' WHERE shipper_id = {$shipperId}";
				mysqli_query($this->mysql, $query);
				if (mysqli_affected_rows($this->mysql) == 1) {
					return Response::getMessageResponseWithMessage("Cập nhật thông tin thành công.");
				} else {
					return Response::getNormalError();
				}
			} else {
				return Response::getSQLConnectionError();
			}
		}

		function addDeposit($id, $pay) {
			if ($this->mysql) {
					$paymentInfo = $this->verifyPayPalPayment($pay);
					if (isset($paymentInfo->id) && isset($paymentInfo->state)) {
						if ($paymentInfo->state == "approved") {
							$amount = $paymentInfo->transactions[0]->amount->total;
							$amount = (int)($amount * 22000);
							$query = "UPDATE shipper SET deposit = deposit + {$amount} WHERE shipper_id = {$id}";
							mysqli_query($this->mysql, $query);
							if (mysqli_affected_rows($this->mysql) > 0) {
								return new Response(200, new MessageResponse("Nạp tiền thành công. Vui lòng kiểm tra lại tài khoản."));
							} else {
								return new Response(678, new ApiError(678, "Xãy ra lỗi. Vui lòng thử lại sau."));
							}
						} else {
							return new Response(678, new ApiError(678, "Thanh toán không được xác nhận. Vui lòng thử lại sau."));
						}
					} else {
						return new Response(678, new ApiError(678, "Quá trình xác nhận thanh toán bị lỗi. Vui lòng thử lại sau"));
					}
			} else {
				return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		function getCheckedOrders($token, $page) {
			$shipperId = $this->getShipperIdFromToken($token);
			switch ($shipperId) {
				case -2:
					return new Response(678, new ApiError(678, "Không thể kết nối tới cơ sở dữ liệu của server."));	

				case -1:
					return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));			
				default:
					$ignore = ($page - 1) * 20;
					$query = "SELECT bill.bill_id, bill.bill_status, billPrice(bill.bill_id) as bill_price, store.store_name, store.store_image FROM bill INNER JOIN store ON bill.bill_store_id = store.store_id WHERE bill.bill_status = 1 ORDER BY bill.bill_time DESC LIMIT {$ignore}, 20";
					$passResultQuery = "SELECT bill.bill_id from bill WHERE bill.bill_status = 1";
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
		}

		function takeCheckedBill($token, $bill_id) {
			$shipperId = $this->getShipperIdFromToken($token);
			switch ($shipperId) {
				case -2:
					return new Response(678, new ApiError(678, "Không thể kết nối tới cơ sở dữ liệu của server."));	

				case -1:
					return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));			
				default:
					$query = "UPDATE bill join shipper SET bill.bill_shipper_id = shipper.shipper_id, bill.bill_status = 2, shipper.deposit = shipper.deposit - bill.bill_ship_price * 0.2 WHERE bill.bill_id = {$bill_id} AND bill.bill_status = 1 AND shipper.shipper_id = {$shipperId} AND checkDeposit({$shipperId}, bill.bill_ship_price * 0.2) AND bill.bill_shipper_id IS NULL";
					mysqli_query($this->mysql, $query);
					if (mysqli_affected_rows($this->mysql) == 2) {
						return new Response(200, new MessageResponse("Nhận đơn hàng thành công."));
					} else {
						return new Response(678, new ApiError(678, "Xãy ra lỗi. Vui lòng thử lại sau."));
					}
			}
		}

		function getBillInfo($token, $id) {
			$userId = $this->getShipperIdFromToken($token);
			switch ($userId) {
				case -1:
					return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));

				case -2:
					return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
				
				default:
					if ($this->mysql) {
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
							$bill->request_shipper = $userId;
							return new Response(200, $bill);
						} else {
							return new Response(678, new ApiError(678, "Xãy ra lỗi! Vui lòng thử lại sau."));
						}
					} else {
						return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
					}
			}
		}

		function completedBill($token, $billId, $confirmCode) {
			$shipperId = $this->getShipperIdFromToken($token);
			switch ($shipperId) {
				case -2:
					return new Response(678, new ApiError(678, "Không thể kết nối tới cơ sở dữ liệu của server."));	

				case -1:
					return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));			
				default:
					$query = "UPDATE bill inner join shipper ON bill.bill_shipper_id = shipper.shipper_id SET bill.bill_status = 3, shipper.deposit = shipper.deposit + bill.bill_ship_price WHERE shipper.shipper_id = {$shipperId} AND bill.bill_id = {$billId} AND bill.confirm_code = {$confirmCode} AND bill.online_payment = 1 AND bill.bill_status = 2"; 
					mysqli_query($this->mysql, $query);
					if (mysqli_affected_rows($this->mysql) == 2) {
						return new Response(200, new MessageResponse("Xác nhận thành công."));
					}
					$query = "UPDATE bill set bill.bill_status = 3 WHERE bill.bill_id = {$billId} AND bill.bill_status = 2 AND bill.online_payment = 0";
					mysqli_query($this->mysql, $query);
					if (mysqli_affected_rows($this->mysql) == 1) {
						return new Response(200, new MessageResponse("Xác nhận thành công."));
					} else {
						return new Response(678, new ApiError(678, "Xãy ra lỗi. Vui lòng thử lại sau."));
					}
			}
		}

		function getTakedBills($token, $page, $status) {
			$shipperId = $this->getShipperIdFromToken($token);
			switch ($shipperId) {
				case -2:
					return new Response(678, new ApiError(678, "Không thể kết nối tới cơ sở dữ liệu của server."));	

				case -1:
					return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));			
				default:
					$ignore = ($page - 1) * 20;
					$query = "SELECT bill.bill_id, bill.bill_status, billPrice(bill.bill_id) as bill_price, store.store_name, store.store_image FROM bill INNER JOIN store ON bill.bill_store_id = store.store_id WHERE bill.bill_status = {$status} AND bill.bill_shipper_id = {$shipperId} ORDER BY bill.bill_time DESC LIMIT {$ignore}, 20";
					$passResultQuery = "SELECT bill.bill_id from bill WHERE bill.bill_status = {$status} AND bill.bill_shipper_id = {$shipperId}";
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
		}

		function setCurrentLocation($location) {
			date_default_timezone_set("Asia/Bangkok");
			$currentTime = ((int)(microtime(true))) * 1000 + 7 * 60 * 60000;
			$shipperId = $this->getShipperIdFromToken($location->token);
			$query = "UPDATE shipper SET current_lat = {$location->lat}, current_lng = {$location->lng}, last_location_modify = {$currentTime} WHERE shipper_id = {$shipperId}";
			mysqli_query($this->mysql, $query);
			if (mysqli_affected_rows($this->mysql) == 1){
				return new Response(200, new MessageResponse("Cập nhật thành công."));
			} else {
				return new Response(678, new ApiError(678, "Cập nhật thất bại."));
			}
		}

		function verifyCash($paymentBody) {
			if ($this->mysql) {
				if (isset($paymentBody->shipper_id) && isset($paymentBody->pay_id)) {
					$paymentInfo = $this->verifyPayPalPayment($paymentBody->pay_id);
					if (isset($paymentInfo->id) && isset($paymentInfo->state)) {
						if ($paymentInfo->state == "approved") {
							$amount = $paymentInfo->transactions[0]->amount->total * 22000;
							$query = "UPDATE shipper SET deposit = deposit + {$amount} WHERE shipper_id = {$paymentBody->shipper_id}";
							mysqli_query($this->mysql, $query);
							if (mysqli_affected_rows($this->mysql) > 0) {
								return new Response(200, new MessageResponse("Nạp tiền thành công. Cảm ơn quý khách đã sử dụng dịch vụ."));
							} else {
								return new Response(678, new ApiError(678, "Xãy ra lỗi. Vui lòng thử lại sau."));
							}
						} else {
							return new Response(678, new ApiError(678, "Thanh toán không được xác nhận. Vui lòng thử lại sau."));
						}
					} else {
						return new Response(678, new ApiError(678, "Quá trình xác nhận thanh toán bị lỗi. Vui lòng thử lại sau"));
					}
				} else {
					return new Response(678, new ApiError(678, "Thiếu dữ liệu."));
				}	
			} else {
				return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		function changePassword($shipperId, $oldpass, $pass){
			if ($this->mysql) {
					$query = "UPDATE shipper SET password = '{$pass}' WHERE shipper_id = {$shipperId} AND password = '{$oldpass}'";
					mysqli_query($this->mysql, $query);
					if (mysqli_affected_rows($this->mysql) == 1) {
						return new Response(200, new MessageResponse("Đổi mật khẩu thành công."));
					} else {
						return new Response(678, new ApiError(678, "Mật khẩu cũ không chính xác."));
					}
			}else {
				return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		function requestResetPass($email) {
			if ($this->mysql) {
				$query = "SELECT shipper_id, account FROM shipper WHERE email = '{$email}';";
				$result = mysqli_query($this->mysql, $query);
				if (mysqli_num_rows($result) == 1) {
					$row = $result->fetch_assoc();
					$id = $row['shipper_id'];
					$userName = $row['account'];
					$otp = rand(100000, 999999);
					$otp_time = time();
					$deleteQuery = "DELETE FROM otp WHERE shipper_id = {$id}";
					mysqli_query($this->mysql, $deleteQuery);
					$query = "INSERT INTO otp (shipper_id, otp_code, otp_time) VALUES ($id, $otp, $otp_time)";
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

		function resetPassword($shipperId, $pass, $otp) {
			$checkTime = time() - 300;
			if ($this->mysql) {
				$query = "DELETE FROM otp WHERE shipper_id = {$shipperId} AND otp_code = {$otp} AND otp_time > {$checkTime};";
				mysqli_query($this->mysql, $query);
				if (mysqli_affected_rows($this->mysql) > 0) {
					$token = sha1($shipperId . microtime(true));
					$query = "UPDATE shipper SET password = '{$pass}', token = '{$token}' WHERE shipper_id = $shipperId;";
					mysqli_query($this->mysql, $query);
					if (mysqli_affected_rows($this->mysql) == 1) {
						$query = "DELETE FROM otp WHERE shipper_id = {$shipperId}";
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

		/**
		*
		* Child function.
		**/
		function checkShipper($shipper) {
			$email = $shipper->email;
			$account = $shipper->account;
			$personalId = $shipper->personal_id;
			if ($this->mysql) {
				$query = "SELECT * FROM shipper WHERE email = '{$email}' OR account = '{$account}' OR personal_id = '{$personalId}'";
				$rs = mysqli_query($this->mysql, $query);
				if (mysqli_num_rows($rs) == 0) {
					return "ok";
				} else {
					$row = $rs->fetch_assoc();
					if ($account == $row['account']) {
						return "Tài khoản đã tồn tại";
					}
					if ($email == $row['email']) {
						return "Email đã được đăng ký.";
					}
					if ($personalId == $row['personal_id']) {
						return "Số chứng minh nhân dân đã được đăng ký.";
					}
				}
			} else {
				return "Không thể kết nối đến cơ sở dữ liệu của Serve.";
			}
		}

		function getShipperIdFromToken($token) {
			if ($token == null || $token == "") {
				return -1;
			}
			if ($this->mysql) {
				$query = "SELECT shipper_id FROM shipper WHERE token = '{$token}'";
				$rs = mysqli_query($this->mysql, $query);
				$numRows = mysqli_num_rows($rs);
				switch ($numRows) {
					case 0:
						return -1;
					case 1:
						$row = $rs->fetch_assoc();
						return (int)($row['shipper_id']);
					default:
						$query = "UPDATE shipper SET token = '' WHERE token = '{$token}'";
						mysqli_query($this->mysql, $query);
						return -1;
				}
			} else {
					return -1;
			}
		}

		function verifyPayPalPayment($payId) {
			$uri = 'https://api.sandbox.paypal.com/v1/payments/payment/' . $payId;
			$ch = curl_init($uri);
			curl_setopt_array($ch, array(
    			CURLOPT_HTTPHEADER  => array('Authorization: Bearer A21AAG2xcvlmzZ74U_CMhMGuwsPmHMRBE1gD9kt6ZvbJXtukdcGlbo6OqzTGYeY-2Wm8tcg8jcQuq5ehrlWKNJgIJsJt374_g',
					'Content-Type: application/json'),
    			CURLOPT_RETURNTRANSFER  =>true,
    			CURLOPT_VERBOSE     => 1
				)
			);
			$payment = json_decode(curl_exec($ch));
			curl_close($ch);
			return $payment;
		}
	}
?>
