<?php
	require_once('/storage/ssd3/122/4702122/public_html/model/response/Response.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/MessageResponse.php');

	class StoreDataSource {

		var $mysql;

		function __construct($sql) {
			$this->mysql = $sql;
		}

		function createShipper($shipper) {
			$check = checkShipper($shipper);
			if ($check == "ok") {
				$query = "INSERT INTO shipper ( `full_name`, `account`, `password`, `birth_day`, `address`, `phone_number`, `deposit`, `personal_id`, `email` ) VALUES ( '{$shipper->full_name}', '{$shipper->account}', '{$shipper->password}', '{$shipper->birth_day}', '{$shipper->address}', '{$shipper->phone_number}', '{$shipper->deposit}', '{$shipper->personal_id}', '{$shipper->email}');";
				mysqli_query($this->mysql, $query);
				if (mysqli_affected_rows($this->mysql) == 1) {
					return new Response(200, MessageResponse("Tạo tài khoản thành công."));
				} else {
					return new Response(200, MessageResponse("Xãy ra lỗi. Vui lòng thử lại sau."));
				}
			} else {
					return new Response(200, MessageResponse($check));
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
	}
?>
