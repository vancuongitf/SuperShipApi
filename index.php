<?php
	date_default_timezone_set("Asia/Bangkok");
	echo date('h');
	echo "---";
	echo date('i');
	echo "---";
	echo date('w');
	echo "---";
	echo microtime(true)*10000;


$path = getcwd();
echo "This Is Your Absolute Path: ";
echo $path;
?>