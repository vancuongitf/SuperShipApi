<?php
	require_once('/storage/ssd3/122/4702122/public_html/model/response/Response.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/ApiError.php');
	require_once('/storage/ssd3/122/4702122/public_html/model/response/MessageResponse.php');

	$response = null;
	$check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check !== false) {
    	$target_dir = "/storage/ssd3/122/4702122/public_html/uploads/";
    	$fileName =  (microtime(true)*10000) . basename($_FILES["image"]["name"]);
		$target_file = $target_dir . $fileName;
    	move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
		$response = new Response(200, new MessageResponse($fileName));        
    } else {
        $response = new Response(678, new ApiError(678, "Chỉ chấp nhận file hình ảnh."));
    }
    header($response->code);
	echo json_encode($response->value);
?>
