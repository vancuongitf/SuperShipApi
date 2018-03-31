<?php
	require_once('/storage/ssd3/122/4702122/public_html/model/response/Response.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
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

		function getStoreInfo($storyId) {
			$store = null;
			
			if ($this->mysql) {
				$query = "SELECT store.store_id, store.store_user_id, store.store_name, store.store_address, store.store_lat, store.store_lng, store.store_phone, store.store_email, store.store_image, store.store_open_day, store.store_open_hour, store.store_close_hour, SUM(rate.rate_value) / COUNT(rate.rate_store_id) as rate_value, COUNT(rate.rate_store_id) as rate_count FROM store LEFT JOIN rate ON store.store_id = rate.rate_store_id WHERE store_id = {$storyId};";
				$result = mysqli_query($this->mysql, $query);
				if (mysqli_num_rows($result) == 1) {
					$row = $result->fetch_assoc();
					$store = new Store($row);
					$store->menu = $this->getMenu($store->store_id);
					$store->options = $this->getDrinkOption($store->store_id);
				}
				return new Response(200, $store);
			} else {
				return new Response(678, new ApiError("Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		function getExpressesStore($advanceParam, $page, $lat, $lng){
			$ignore = ($page - 1) * 20;
			$query = "";
			$stores = array();
			// case 1: Tim quanh day.
			if (isset($lat) && isset($lng)) {
				switch ($advanceParam) {
					case 1: // Tim quanh day
						$query = "SELECT store.store_id ,store.store_name ,store.store_address ,store.store_lat ,store.store_lng ,store.store_open_day ,store.store_open_hour, store.store_close_hour ,store.store_image, SUM(rate.rate_value) / COUNT(rate.rate_store_id) as rate_value, COUNT(rate.rate_store_id) as rate_count, getDistance({$lat}, {$lng}, store.store_lat, store.store_lng) as store_distance FROM store LEFT JOIN rate ON store.store_id = rate.rate_store_id GROUP BY store.store_id ORDER BY store_distance ASC LIMIT {$ignore}, 20;";
						break;
					
					case 2: // Dat nhieu
						$query = "SELECT DISTINCT store.store_id ,store.store_name ,store.store_address ,store.store_lat ,store.store_lng ,store.store_open_day ,store.store_open_hour, store.store_close_hour ,store.store_image, getRateValue(store.store_id) as rate_value, getRateCount(store.store_id) as rate_count, getDistance($lat, $lng, store.store_lat, store.store_lng) as store_distance FROM store INNER JOIN bill ON store.store_id = bill.bill_store_id ORDER BY getBillCount(store.store_id) DESC LIMIT {$ignore}, 20;";
						break;

					case 3: // Vua dat
						$query = "SELECT DISTINCT store.store_id ,store.store_name ,store.store_address ,store.store_lat ,store.store_lng ,store.store_open_day ,store.store_open_hour, store.store_close_hour ,store.store_image, getRateValue(store.store_id) as rate_value, getRateCount(store.store_id) as rate_count, getDistance($lat, $lng, store.store_lat, store.store_lng) as store_distance FROM store INNER JOIN bill ON store.store_id = bill.bill_store_id ORDER BY bill.bill_time DESC LIMIT {$ignore}, 20;";
						break;
				}
			} else {
				switch ($advanceParam) {
					case 1: // Tim quanh day
						$query = "SELECT store.store_id ,store.store_name ,store.store_address ,store.store_lat ,store.store_lng ,store.store_open_day ,store.store_open_hour, store.store_close_hour ,store.store_image, SUM(rate.rate_value) / COUNT(rate.rate_store_id) as rate_value, COUNT(rate.rate_store_id) as rate_count FROM store LEFT JOIN rate ON store.store_id = rate.rate_store_id GROUP BY store.store_id LIMIT {$ignore}, 20;";
						break;
					
					case 2: // Dat nhieu
						$query = "SELECT DISTINCT store.store_id ,store.store_name ,store.store_address ,store.store_lat ,store.store_lng ,store.store_open_day ,store.store_open_hour, store.store_close_hour ,store.store_image, getRateValue(store.store_id) as rate_value, getRateCount(store.store_id) as rate_count FROM store INNER JOIN bill ON store.store_id = bill.bill_store_id ORDER BY getBillCount(store.store_id) DESC LIMIT {$ignore}, 20;";
						break;

					case 3: // Vua dat
						$query = "SELECT DISTINCT store.store_id ,store.store_name ,store.store_address ,store.store_lat ,store.store_lng ,store.store_open_day ,store.store_open_hour, store.store_close_hour ,store.store_image, getRateValue(store.store_id) as rate_value, getRateCount(store.store_id) as rate_count FROM store INNER JOIN bill ON store.store_id = bill.bill_store_id ORDER BY bill.bill_time DESC LIMIT {$ignore}, 20;";
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
				return new Response(200 ,$stores);
			} else {
				return new Response(678, new ApiError("Không thể kết nối đến cơ sở dữ liệu của server. Vui lòng thử lại sau."));
			}
		}

		// Search store by name or drink name.
		function search($key, $page, $lat, $lng) {
			$ignore = ($page - 1) * 20;
			$query = "";
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
				return new Response(200 ,$stores);
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
			$drinks = $this->getDrinkByStoreId($storeId);
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
			return new Menu($drinks, $sub_menus);
		}

		function getDrink($menuId) {
			$drinks = array();
			if ($this->mysql) {
				$query_drink = "SELECT * FROM drink WHERE drink_menu_id = {$menuId};";
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

		function getDrinkByStoreId($store_id) {
			$drinks = array();
			if ($this->mysql) {
				$query_drink = "SELECT * FROM drink WHERE store_id = {$store_id} AND drink_menu_id IS NULL;";
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
	}
?>
