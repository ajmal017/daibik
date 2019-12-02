<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "surya";
$table = "volume_analysis";

//$symbol = trim($_GET['symbol']);
//$symbol = str_replace('_', '&', $symbol);
$symbol = 'RELINFRA';
//print $symbol . "\r\n";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error || empty( $symbol )) {
	die("Connection failed: " . $conn->connect_error);
}
else {

	$sql = "SELECT * FROM $table " . " WHERE symbol ='". $symbol . "'";
	$query=mysqli_query($conn,$sql);
	while ( $result = mysqli_fetch_array($query,MYSQLI_ASSOC) ) {
		$data[] = $result;
	}

	$data = array_reverse( $data );
	print '<pre>';
	print_r($data);

	$start_index = $end_index = 0;
	foreach ( $data as $key => $value ) {
       if ( $value['percentage_increase'] > 0 ) {
	       $start_index = $key - 1;
       }
	   else {
		   $end_index = $key;
		   $start_index = 0;
	   }
	}
}
