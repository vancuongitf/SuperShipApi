<?php
	class DbConnection {
		
		static function getConnection(){
			$servername = "localhost";
			$username = "id4702122_vancuongitf";
			$password = "20301101";
			$dbname = "id4702122_sm_db";

			// Create connection
			return new mysqli($servername, $username, $password,$dbname);
		}
	}
?>