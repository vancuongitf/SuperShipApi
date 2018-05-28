<?php
	require_once('../../../connect/DbConnection.php');
	require_once('../../../model/response/ApiError.php');
	require_once('../../../datasource/user/UserDataSource.php');
	require_once('../../../util/TextUtil.php');

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
