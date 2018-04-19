<?php
	require_once('/storage/ssd3/122/4702122/public_html/connect/DbConnection.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/datasource/user/UserDataSource.php');
	require_once('/storage/ssd3/122/4702122/public_html/util/TextUtil.php');

	$json = file_get_contents('php://input');
    $user = json_decode($json);
    $response = null;
    if (isset($user)) {
    	if ( TextUtil::validateEmail($user->email)) {
    		if (TextUtil::validateUserName($user->name)) {
    			if (TextUtil::validatePassWord($user->password)) {
    				if (TextUtil::validateFullName($user->full_name)) {
    					if (TextUtil::validatePhoneNumber($user->phone)) {
    						$active_key = md5(microtime(true));
    						$user->active_key = $active_key;
    						$userDataSource = new UserDataSource(DbConnection::getConnection());
							$response = $userDataSource->createUser($user);
							if ($response->code == "HTTP/1.1 200 OK") {
								mail($user->email,"Kích Hoạt Tài Khoản","https://vnshipperman.000webhostapp.com/user/active.php?user={$user->name}&active_key={$active_key}");
							}
    					} else {
    						$apiError = new ApiError(678, "Số điện thoại không hợp lệ.");
    						$response = new Response(678, $apiError);
    					} 
    				} else {
    					$apiError = new ApiError(678, "Tên không hợp lệ.");
    					$response = new Response(678, $apiError);
    				}
    			} else {
    				$apiError = new ApiError(678, "Password không hợp lệ.");
    				$response = new Response(678, $apiError);
    			}
    		} else {
    			$apiError = new ApiError(678, "Tài khoản không hợp lệ.");
    			$response = new Response(678, $apiError);
    		}
    	} else {
    		$apiError = new ApiError(678, "Email không hợp lệ.");
    		$response = new Response(678, $apiError);
    	}
    } else {
    	$apiError = new ApiError(678, "Missing data.");
		$response = new Response(678, $apiError);
    }
    header($response->code);
	echo json_encode($response->value);
?>