<?php
	$path = getcwd();
	$paths = explode("public_html", $path);
	$basePath = $paths[0];
	require_once($basePath . 'public_html/model/response/Response.php');
	require_once($basePath . 'public_html/model/response/MessageResponse.php');
	require_once($basePath . 'public_html/model/response/ApiError.php');
	require_once($basePath . 'public_html/model/store/ExpressBill.php');
	require_once($basePath . 'public_html/model/response/BillListResponse.php');
	require_once($basePath . 'public_html/model/store/Bill.php');
	require_once($basePath . 'public_html/model/store/Location.php');
	require_once($basePath . 'public_html/model/store/OrderedDrink.php');
	require_once($basePath . 'public_html/util/const/Constant.php');

	class OrderDataSource {

		var $mysql;

		function __construct($sql) {
			$this->mysql = $sql;
		}


		function orderDrink($orderBody) {
			$userId = $orderBody->user_id;
			$billId = (int)(microtime(true)*10000);
			$drinks = $orderBody->drinks;
			date_default_timezone_set("Asia/Bangkok");
			if ($this->mysql) {
				$orderAddress = $orderBody->address;
				$latLng = $orderAddress->lat_lng;
				$time = time() * 1000;
				$confirmCode = rand(100000, 999999);
				$query = "INSERT INTO `bill` (`bill_id`, `bill_store_id`, bill_user_id,`bill_user_name`, `bill_user_phone`, `bill_address`, `bill_lat`, `bill_lng`, `bill_ship_road`, `bill_time`, `bill_ship_price`, `bill_status`, `confirm_code`) VALUES ({$billId}, {$orderBody->store_id}, {$userId},'{$orderBody->user_name}', '{$orderBody->user_phone}', '{$orderAddress->address}', {$latLng->latitude}, {$latLng->longitude}, '{$orderBody->ship_road}', {$time}, {$orderBody->ship_price}, 0, $confirmCode);";
				mysqli_query($this->mysql, $query);
				if (mysqli_affected_rows($this->mysql) == 1) {
					$drinkQuery = "INSERT INTO `bill_drink` (`id`, `bill_id`, `drink_id`, `drink_name`, `drink_image`, `drink_price`, `note`, `drink_option`, `drink_count`) VALUES ";
					$drinksCount = count($drinks);
					$valuesQuery = "";
					for ($i = 0; $i < $drinksCount; $i++) {
						$drink = $drinks[$i];
						$id = (int)(microtime(true)*10000 . rand(1, 9999));
						$drinkId = $drink->drink_id;
						$name = $drink->drink_name;
						$price = $drink->drink_price;
						$image = $drink->drink_image;
						$count = $drink->count;
						$note = $drink->note;
						$options = $drink->drink_options;
						$drinkOptions = json_encode($options, JSON_UNESCAPED_UNICODE);
						$valuesQuery =$valuesQuery . " ({$id}, {$billId}, {$drinkId}, '{$name}','{$image}', {$price}, '{$note}', '{$drinkOptions}', {$count})";
						if ($i < $drinksCount - 1) {
							$valuesQuery = $valuesQuery . ", ";
						}
					}
					$drinkQuery = $drinkQuery . $valuesQuery;
					mysqli_query($this->mysql, $drinkQuery);
					if (mysqli_affected_rows($this->mysql) == $drinksCount) {
						return new Response(200, new MessageResponse("Đặt hàng thành công. Vui lòng đợi điện thoại xác nhận của nhân viên tổng đài."));
					} else {
						return new Response(678, new ApiError(678, "Xãy ra lỗi! Vui lòng thử lại sau."));
					}
				} else {
					return new Response(678, new ApiError(678, "Xãy ra lỗi! Vui lòng thử lại sau."));
				}
			} else {
				return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		function getUserOrders($token, $page) {
			if ($this->mysql) {
				$userId = $this->getUserIdFromToken($token);
				switch ($userId) {
					case -1:
						return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));
					
					case -2:
						return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));

					default:
						return $this->getUserOrdersById($userId, $page);
				}
			} else {
				return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		function getUserOrdersById($userId, $page) {
			if ($this->mysql) {
				$ignore = ($page - 1) * 20;
				$nexPageFlag = false;
				$query = "SELECT bill.bill_id, bill.bill_status, billPrice(bill.bill_id) as bill_price, store.store_name, store.store_image FROM bill INNER JOIN store ON bill.bill_store_id = store.store_id WHERE bill.bill_user_id = {$userId} ORDER BY bill.bill_time DESC LIMIT {$ignore}, 20";
				$passResultQuery = "SELECT bill.bill_id from bill WHERE bill.bill_user_id = {$userId}";
				$bills = array();
				$result = mysqli_query($this->mysql, $query);
				if (mysqli_num_rows($result) > 0) {
					while ($row = $result->fetch_assoc()) {
						array_push($bills, new ExpressBill($row));
					}
					if (mysqli_num_rows(mysqli_query($this->mysql, $passResultQuery)) > $page * 20) {
						$nexPageFlag = true;
					}
				}
				return new response(200, new BillListResponse($nexPageFlag, $bills));
			} else {
				return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		function getBillInfo($token, $id) {
			$userId = $this->getUserIdFromToken($token);
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
							$bill->request_shipper = 1;
							return new Response(200, $bill);
						} else {
							return new Response(678, new ApiError(678, "Xãy ra lỗi! Vui lòng thử lại sau."));
						}
					} else {
						return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
					}
			}
		}

		function verifyPayment($paymentBody) {
			if ($this->mysql) {
				if (isset($paymentBody->user_id) && isset($paymentBody->bill_id) && isset($paymentBody->pay_id)) {
					$paymentInfo = $this->verifyPayPalPayment($paymentBody->pay_id);
					if (isset($paymentInfo->id) && isset($paymentInfo->state)) {
						if ($paymentInfo->state == "approved") {
							$amount = $paymentInfo->transactions[0]->amount->total;
							$query = "INSERT INTO online_payment (user_id, bill_id, pay_id, pay_time, amount) VALUES ({$paymentBody->user_id}, {$paymentBody->bill_id}, '{$paymentBody->pay_id}', '{$paymentInfo->create_time}', {$paymentInfo->transactions[0]->amount->total})";
							mysqli_query($this->mysql, $query);
							if (mysqli_affected_rows($this->mysql) > 0) {
								return new Response(200, new MessageResponse("Thanh toán thành công. Cảm ơn quý khách đã sử dụng dịch vụ."));
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

		function getLiveLocation($token, $id) {
			$userId = $this->getUserIdFromToken($token);
			switch ($userId) {
				case -1:
					return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));

				case -2:
					return new Response(678, new ApiError(678, "Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
				
				default:
					$query = "SELECT currentBillLat(bill.bill_id) as lat,currentBillLng(bill.bill_id) as lng, lastModifyLocation(bill.bill_id) as last_modify from bill WHERE bill.bill_id = $id AND bill.bill_status = 2 AND bill.bill_user_id = {$userId}";
					$result = mysqli_query($this->mysql, $query);
					if (mysqli_num_rows($result) == 1) {
						return new Response(200, new Location($result->fetch_assoc()));
					}
					return new Response(678, new ApiError(678,"Xãy ra lỗi. Vui lòng thử lại sau."));


			}
		}

		/**
		*
		*	CHILD FUNCTION FOR getStoreInfo.
		*
		**/

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
						$query = "UPDATE user SET user_token = '' WHERE user_token = $token";
						mysqli_query($this->mysql, $query);
						return -1;
				}
			} else {
					return -2;
			}
		}

		function verifyPayPalPayment($payId) {
			$uri = 'https://api.sandbox.paypal.com/v1/payments/payment/' . $payId;
			$ch = curl_init($uri);
			$payPalToken = 
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
