<?php
	// $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
 //    if($check !== false) {
 //    	$target_dir = "/storage/ssd3/122/4702122/public_html/uploads/";
	// 	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
 //    	move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
 //        echo "File is an image - " . $check["mime"] . ".";
 //        $uploadOk = 1;
 //    } else {
 //        echo "File is not an image.";
 //        $uploadOk = 0;
 //    }

 //    $array = array();
 //    array_push($array, 1);
 //    array_push($array, 2);
 //    array_push($array, 3);
 //    array_push($array, 4);
 //    array_push($array, 5);
 //    array_push($array, 6);
 //    $str = json_encode($array);
 //    echo "xxx" . $str . "xxx";
 //    $array2 = json_decode($str);
 //    foreach ($array2 as $value) {
 //    	echo "----" . $value . "----";
 //    }
        $payId = "PAY-6V193761LE940791ULL2B73I";
        $uri = 'https://api.sandbox.paypal.com/v1/payments/payment/' . $payId;
            $ch = curl_init($uri);
            curl_setopt_array($ch, array(
                CURLOPT_HTTPHEADER  => array('Authorization: Bearer A21AAH98TVnuihKtxh8zJlUanlRv2KosCe2tbnZwLfu8m0dXg8E1tJXEJazerU6M9wX8dMBJB2Eeh-F24tqSq7WGxwJ1GqlCw',
                    'Content-Type: application/json'),
                CURLOPT_RETURNTRANSFER  =>true,
                CURLOPT_VERBOSE     => 1
                )
            );
            $payment = json_decode(curl_exec($ch));
            curl_close($ch);
            

            echo json_encode(editDrink);
    // $opts = array(
    //     'https'=>array(
    //         'method'=>"GET",
    //         'header'=>"Authorization: Bearer A21AAEBu12JnLNU62aXf1EpI3XnugFjDfYzwOrCCnzs_DUcLHDB5QD3ML5lOAUdNyDS_W9Tb2C5K1yvDF4uCiASZLV4hkMtuw\r\n" 
    //                 . "Content-Type: application/json\r\n"
    //      )
    // );

    // $context = stream_context_create($opts);

    // // Open the file using the HTTP headers set above
    // $file = file_get_contents('https://api.sandbox.paypal.com/v1/payments/payment/PAY-98V62268S8447904WLLYAIUQ', false, $context);

    // echo json_encode($file);
?>
