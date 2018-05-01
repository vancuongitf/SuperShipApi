<?php
	$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
    	$target_dir = "/storage/ssd3/122/4702122/public_html/uploads/";
		$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    	move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    $array = array();
    array_push($array, 1);
    array_push($array, 2);
    array_push($array, 3);
    array_push($array, 4);
    array_push($array, 5);
    array_push($array, 6);
    $str = json_encode($array);
    echo "xxx" . $str . "xxx";
    $array2 = json_decode($str);
    foreach ($array2 as $value) {
    	echo "----" . $value . "----";
    }
?>
