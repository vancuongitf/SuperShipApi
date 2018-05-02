<?php
	require_once('/storage/ssd3/122/4702122/public_html/model/response/Response.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/MessageResponse.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/StoreListResponse.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/store/Store.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/store/Menu.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/store/SubMenu.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/store/Drink.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/store/DrinkOption.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/store/DrinkOptionItem.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/store/StoreExpress.php');
	class StoreDataSource {

		var $mysql;

		function __construct($sql) {
			$this->mysql = $sql;
		}

		function createStore($store) {
			if ($this->mysql) {

				$userId = $this->getUserIdFromToken($store->token);
				if ($userId == -1) {
					return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));
				}
				$name = $store->store_name;
				$unAccentName = $store->store_un_accent_name;
				$address = $store->store_address;
				$lat = $store->store_lat_lng->latitude;
				$lng = $store->store_lat_lng->longitude;
				$phone = $store->store_phone;
				$email = $store->store_email;
				$image = $store->store_image;
				$opendays = json_encode($store->store_open_time->open_days);
				$openHour = $store->store_open_time->open;
				$clsoeHour = $store->store_open_time->close;
				$query = "INSERT INTO `store` (`store_user_id`, `store_name`, `store_name_un_accent`, `store_address`, `store_lat`, `store_lng`, `store_phone`, `store_email`, `store_image`, `store_open_day`, `store_open_hour`, `store_close_hour`) VALUES ($userId, '{$name}', '{$unAccentName}', '{$address}', {$lat}, {$lng}, '{$phone}', '{$email}', '{$image}', '{$opendays}', {$openHour}, {$clsoeHour});";
				mysqli_query($this->mysql, $query);
				if (mysqli_affected_rows($this->mysql) == 1) {
					return new Response(200, new MessageResponse("Chúc mừng bạn đã tạo cửa hàng thành công."));
				} else {
					return new Response(678, new ApiError(678, "Xãy ra lỗi. Vui lòng thử lại sau."));
				}
			} else {
				return new Response(678, new ApiError("Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		function createDrink($drink) {
			if ($this->mysql) {
				$userId = $this->getUserIdFromToken($drink->token);
				switch ($userId) {
					case -1:
						return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));
						
					case -2:
						return new Response(678, new ApiError("Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
					default:
						$storeId = $drink->store_id;
						$name = $drink->name;
						$unAccentName = $drink->un_accent_name;
						$price = $drink->price;
						$image = $drink->image;

						$query = "INSERT INTO `drink` (`store_id`, `drink_name`, `drink_name_un_accent`, `drink_price`, `drink_image`) VALUES ({$storeId}, '{$name}', '{$unAccentName}', {$price}, '{$image}');";
						mysqli_query($this->mysql, $query);
						if (mysqli_affected_rows($this->mysql) == 1) {
							return new Response(200, new MessageResponse("Thêm đồ uống thành công."));
						} else {
							return new Response(678, new ApiError(678, "Xãy ra lỗi! Vui lòng thử lại sau."));
						}
				}
			} else {
				return new Response(678, new ApiError("Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		function createDrinkOption($drinkOption){
			if ($this->mysql) {
				$userId = $this->getUserIdFromToken($drinkOption->token);
				switch ($userId) {
					case -1:
						return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));
						
					case -2:
						return new Response(678, new ApiError("Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
					default:
						$optionId = (microtime(true)*10000);
						$storeId = $drinkOption->store_id;
						$name = $drinkOption->name;
						$multiChoose = $drinkOption->multi_choose;

						$query = "INSERT INTO `drink_option` (`drink_option_id`, `drink_option_store_id`, `drink_option_name`, `drink_option_mutil_choose`) VALUES ({$optionId}, {$storeId}, '{$name}', {$multiChoose});";
						mysqli_query($this->mysql, $query);
						if (mysqli_affected_rows($this->mysql) == 1) {
							return new Response(200, new MessageResponse("{$optionId}"));
						} else {
							return new Response(678, new ApiError(678, "Xãy ra lỗi! Vui lòng thử lại sau."));
						}
				}
			} else {
				return new Response(678, new ApiError("Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		function addDrinkOptionItem($drinkOptionItem) {
			if ($this->mysql) {
				$userId = $this->getUserIdFromToken($drinkOptionItem->token);
				switch ($userId) {
					case -1:
						return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));
						
					case -2:
						return new Response(678, new ApiError("Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
					default:
						$valuesQuery = "";
						$items = $drinkOptionItem->items;
						$size = count($items);
						for ($i = 0; $i < $size; $i++) {
							$item = $items[$i];
							$drinkOptionId = $item->drink_option_id;
							$name = $item->name;
							$price = $item->price;
							$valuesQuery = $valuesQuery . " ({$drinkOptionId}, '{$name}', {$price})";
							if ($i < $size - 1) {
								$valuesQuery = $valuesQuery . ", ";
							}
						}

						$query = "INSERT INTO `drink_option_item` ( `drink_option_id`, `drink_option_item_name`, `drink_option_item_price`) VALUES " . $valuesQuery . ";";
						mysqli_query($this->mysql, $query);
						if (mysqli_affected_rows($this->mysql) == $size) {
							return new Response(200, new MessageResponse("Thêm tùy chọn thành công."));
						} else {
							return new Response(678, new ApiError(678, "Xãy ra lỗi! Vui lòng thử lại sau."));
						}
				}
			} else {
				return new Response(678, new ApiError("Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		function getStoreInfo($storyId) {
			$store = null;
			
			if ($this->mysql) {
				$query = "SELECT store.store_id, store.store_user_id, store.store_name, store.store_address, store.store_lat, store.store_lng, store.store_phone, store.store_email, store.store_image, store.store_open_day, store.store_open_hour, store.store_close_hour, SUM(rate.rate_value) / COUNT(rate.rate_store_id) as rate_value, COUNT(rate.rate_store_id) as rate_count FROM store LEFT JOIN rate ON store.store_id = rate.rate_store_id WHERE store_id = {$storyId};";
				$result = mysqli_query($this->mysql, $query);
				if (mysqli_num_rows($result) == 1) {
					$row = $result->fetch_assoc();
					$store = new Store($row);
					$store->menu = $this->getDrink($store->store_id);
					$store->options = $this->getDrinkOption($store->store_id);
					$store->isOpening = $this->isOpening($store->store_open_time);
				}
				return new Response(200, $store);
			} else {
				return new Response(678, new ApiError("Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		function getExpressesStore($advanceParam, $page, $lat, $lng){
			$ignore = ($page - 1) * 20;
			$query = "";
			$passResultQuery = "";
			$stores = array();
			// case 1: Tim quanh day.
			if (isset($lat) && isset($lng)) {
				switch ($advanceParam) {
					case 1: // Tim quanh day
						$query = "SELECT DISTINCT store.store_id ,store.store_name ,store.store_address ,store.store_lat ,store.store_lng ,store.store_open_day ,store.store_open_hour, store.store_close_hour ,store.store_image, SUM(rate.rate_value) / COUNT(rate.rate_store_id) as rate_value, COUNT(rate.rate_store_id) as rate_count, getDistance({$lat}, {$lng}, store.store_lat, store.store_lng) as store_distance FROM store LEFT JOIN rate ON store.store_id = rate.rate_store_id GROUP BY store.store_id ORDER BY store_distance ASC LIMIT {$ignore}, 20;";
						$passResultQuery = "SELECT store.store_id FROM store;";
						break;
					
					case 2: // Dat nhieu
						$query = "SELECT DISTINCT store.store_id ,store.store_name ,store.store_address ,store.store_lat ,store.store_lng ,store.store_open_day ,store.store_open_hour, store.store_close_hour ,store.store_image, getRateValue(store.store_id) as rate_value, getRateCount(store.store_id) as rate_count, getDistance($lat, $lng, store.store_lat, store.store_lng) as store_distance FROM store WHERE getBillCount(store.store_id) > 0 ORDER BY getBillCount(store.store_id) DESC LIMIT {$ignore}, 20;";
						$passResultQuery = "SELECT store.store_id FROM store WHERE getBillCount(store.store_id) > 0;";
						break;

					case 3: // Vua dat
						$query = "SELECT DISTINCT store.store_id ,store.store_name ,store.store_address ,store.store_lat ,store.store_lng ,store.store_open_day ,store.store_open_hour, store.store_close_hour ,store.store_image, getRateValue(store.store_id) as rate_value, getRateCount(store.store_id) as rate_count, getDistance($lat, $lng, store.store_lat, store.store_lng) as store_distance FROM store WHERE lastestBill(store.store_id) IS NOT NULL ORDER BY lastestBill(store.store_id) DESC LIMIT {$ignore}, 20;";
						$passResultQuery = "SELECT DISTINCT store.store_id FROM store WHERE lastestBill(store.store_id) IS NOT NULL;";
						break;
				}
			} else {
				switch ($advanceParam) {
					case 1: // Tim quanh day
						$query = "SELECT DISTINCT store.store_id ,store.store_name ,store.store_address ,store.store_lat ,store.store_lng ,store.store_open_day ,store.store_open_hour, store.store_close_hour ,store.store_image, SUM(rate.rate_value) / COUNT(rate.rate_store_id) as rate_value, COUNT(rate.rate_store_id) as rate_count FROM store LEFT JOIN rate ON store.store_id = rate.rate_store_id GROUP BY store.store_id LIMIT {$ignore}, 20;";
						$passResultQuery = "SELECT store.store_id FROM store;";
						break;
					
					case 2: // Dat nhieu
						$query = "SELECT DISTINCT store.store_id ,store.store_name ,store.store_address ,store.store_lat ,store.store_lng ,store.store_open_day ,store.store_open_hour, store.store_close_hour ,store.store_image, getRateValue(store.store_id) as rate_value, getRateCount(store.store_id) as rate_count FROM store WHERE getBillCount(store.store_id) > 0 ORDER BY getBillCount(store.store_id) DESC LIMIT {$ignore}, 20;";
						$passResultQuery = "SELECT store.store_id FROM store WHERE getBillCount(store.store_id) > 0;";
						break;

					case 3: // Vua dat
						$query = "SELECT DISTINCT store.store_id ,store.store_name ,store.store_address ,store.store_lat ,store.store_lng ,store.store_open_day ,store.store_open_hour, store.store_close_hour ,store.store_image, getRateValue(store.store_id) as rate_value, getRateCount(store.store_id) as rate_count FROM store WHERE lastestBill(store.store_id) IS NOT NULL ORDER BY lastestBill(store.store_id) DESC LIMIT {$ignore}, 20;";
						$passResultQuery = "SELECT DISTINCT store.store_id FROM store WHERE lastestBill(store.store_id) IS NOT NULL;";
						break;
				}
			}
			if ($this->mysql) {
				$result = mysqli_query($this->mysql, $query);
				if (mysqli_num_rows($result) > 0) {
					while($row = $result->fetch_assoc()) {
						array_push($stores, new StoreExpress($row));
					}
				}
				$passResult = mysqli_query($this->mysql, $passResultQuery);
				$nextPage = false;
				if (mysqli_num_rows($passResult) > $page * 20) {
					$nextPage = true;
				}
				return new Response(200 ,new StoreListResponse($nextPage, $stores));
			} else {
				return new Response(678, new ApiError("Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		function getExpressStoreByAccessToken($token, $page) {
			if ($token == null || $token == "") {
				return new Response(401, ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));
			}
			if ($this->mysql) {
				$query = "SELECT user_id FROM user WHERE user_token = '{$token}'";
				$rs = mysqli_query($this->mysql, $query);
				$numRows = mysqli_num_rows($rs);
				switch ($numRows) {
					case 0:
						return new Response(401, new ApiError(401, "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại để tiếp tục."));

					case 1:
						$row = $rs->fetch_assoc();
						return $this->getExpressStoreByUserId($row['user_id'], $page);
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

		function getExpressStoreByUserId($userId, $page) {
			$ignore = ($page - 1) * 20;
			$stores = array();
			if ($this->mysql) {
				$query = "SELECT DISTINCT store.store_id ,store.store_name ,store.store_address ,store.store_lat ,store.store_lng ,store.store_open_day ,store.store_open_hour, store.store_close_hour ,store.store_image, SUM(rate.rate_value) / COUNT(rate.rate_store_id) as rate_value, COUNT(rate.rate_store_id) as rate_count FROM store LEFT JOIN rate ON store.store_id = rate.rate_store_id WHERE store.store_user_id = {$userId} GROUP BY store.store_id LIMIT {$ignore}, 20;";
				$passResult = "SELECT store.store_id from store WHERE store.store_user_id = {$userId};";
				$result = mysqli_query($this->mysql, $query);
				if (mysqli_num_rows($result) > 0) {
					while($row = $result->fetch_assoc()) {
						array_push($stores, new StoreExpress($row));
					}
				}
				$passResult = mysqli_query($this->mysql, $passResult);
				$nextPage = false;
				if (mysqli_num_rows($passResult) > $page * 20) {
					$nextPage = true;
				}
				return new Response(200 ,new StoreListResponse($nextPage, $stores));
			} else {
				return new Response(678, new ApiError("Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		// Search store by name or drink name.
		function search($key, $page, $lat, $lng) {
			$ignore = ($page - 1) * 20;
			$query = "";
			$passResultQuery = "SELECT DISTINCT store.store_id FROM store LEFT JOIN drink ON store.store_id = drink.store_id WHERE stringContains(store.store_name_un_accent, '{$key}') OR stringContains(drink.drink_name_un_accent,'{$key}');";
			$stores = array();
			if (isset($lat) && isset($lng)) {
				$query = "SELECT DISTINCT store.store_id ,store.store_name ,store.store_address ,store.store_lat ,store.store_lng ,store.store_open_day ,store.store_open_hour, store.store_close_hour ,store.store_image, getRateValue(store.store_id) as rate_value, getRateCount(store.store_id) as rate_count, getDistance($lat, $lng, store.store_lat, store.store_lng) as store_distance FROM store LEFT JOIN drink ON store.store_id = drink.store_id WHERE stringContains(store.store_name_un_accent, '{$key}') OR stringContains(drink.drink_name_un_accent,'{$key}') LIMIT {$ignore}, 20;";
			} else {
				$query = "SELECT DISTINCT store.store_id ,store.store_name ,store.store_address ,store.store_lat ,store.store_lng ,store.store_open_day ,store.store_open_hour, store.store_close_hour ,store.store_image, getRateValue(store.store_id) as rate_value, getRateCount(store.store_id) as rate_count FROM store LEFT JOIN drink ON store.store_id = drink.store_id WHERE stringContains(store.store_name_un_accent, '{$key}') OR stringContains(drink.drink_name_un_accent,'{$key}') LIMIT {$ignore}, 20;";
			}
			if ($this->mysql) {
				$result = mysqli_query($this->mysql, $query);
				if (mysqli_num_rows($result) > 0) {
					while($row = $result->fetch_assoc()) {
						array_push($stores, new StoreExpress($row));
					}
				}
				$passResult = mysqli_query($this->mysql, $passResultQuery);
				$nextPage = false;
				if (mysqli_num_rows($passResult) > $page * 20) {
					$nextPage = true;
				}
				return new Response(200 ,new StoreListResponse($nextPage, $stores));
			} else {
				return new Response(678, new ApiError("Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		/**
		*
		*	CHILD FUNCTION FOR getStoreInfo.
		*
		**/
		function getMenu($storeId) {
			$sub_menus = array();
			if ($this->mysql) {
				$query_menu = "SELECT * FROM menu WHERE menu_store_id = {$storeId};";
				$result = mysqli_query($this->mysql, $query_menu);
				if (mysqli_num_rows($result) > 0) {
					while ($row = $result->fetch_assoc()) {
						$menu = new SubMenu($row);
						$menu->drinks = $this->getDrink($menu->menu_id);
						array_push($sub_menus, $menu);
					}
				}
			}
			return new Menu($sub_menus);
		}

		function getDrink($storeId) {
			$drinks = array();
			if ($this->mysql) {
				$query_drink = "SELECT * FROM drink WHERE store_id = {$storeId};";
				$result = mysqli_query($this->mysql, $query_drink);
				if (mysqli_num_rows($result) > 0) {
					while ($row = $result->fetch_assoc()) {
						$drink = new Drink($row);
						$drink->drink_options = $this->getDrinkOptionByDrinkId($drink->drink_id);
						array_push($drinks, $drink);
					}
				}
			}
			return $drinks;
		}

		function getDrinkOption($storeId) {
			$options = array();
			if ($this->mysql) {
				$query_option = "SELECT * FROM drink_option WHERE drink_option_store_id = {$storeId};";
				$result = mysqli_query($this->mysql, $query_option);
				if (mysqli_num_rows($result) > 0) {
					while ($row = $result->fetch_assoc()) {
						$drinkOption = new DrinkOption($row);
						$drinkOption->drink_option_items = $this->getDrinkOptionItem($drinkOption->drink_option_id);
						array_push($options, $drinkOption);
					}
				}
			}
			return $options;
		}

		function getDrinkOptionByDrinkId($drink_id) {
			$options = array();
			if ($this->mysql) {
				$query = "SELECT * FROM drink_menu_option WHERE drink_id = {$drink_id};";
				$rs = mysqli_query($this->mysql, $query);
				if (mysqli_num_rows($rs) > 0) {
					while($r = $rs->fetch_assoc()) {
						$query_option = "SELECT * FROM drink_option WHERE drink_option_id = {$r['drink_option_id']};";
						$result = mysqli_query($this->mysql, $query_option);
						if (mysqli_num_rows($result) > 0) {
							while ($row = $result->fetch_assoc()) {
								$drinkOption = new DrinkOption($row);
								$drinkOption->drink_option_items = $this->getDrinkOptionItem($drinkOption->drink_option_id);
								array_push($options, $drinkOption);
							}
						}
					}
				}
			}
			return $options;
		}

		function getDrinkOptionItem($drinkOptionId) {
			$items = array();
			if ($this->mysql) {
				$query_option = "SELECT * FROM drink_option_item WHERE drink_option_id = {$drinkOptionId};";
				$result = mysqli_query($this->mysql, $query_option);
				if (mysqli_num_rows($result) > 0) {
					while ($row = $result->fetch_assoc()) {
						array_push($items, new DrinkOptionItem($row));
					}
				}
			}
			return $items;
		}

		function isOpening($openTime) {
			$currentDay = (int) date('w');
			$currentTime = (int) date('H') * 60 + (int) date('i');
			if ($openTime->open <= $currentTime && $currentTime <= $openTime->close) {
				foreach ($openTime->open_days as $value) {
					if ($value == $currentDay) {
						return true;
					}
				}
			}
			return false;
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
						$query = "UPDATE user SET user_token = '' WHERE user_token = $token";
						mysqli_query($this->mysql, $query);
						return -1;
				}
			} else {
					return -2;
			}
		}
	}
?>
