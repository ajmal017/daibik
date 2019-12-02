<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "tojo";
$table = "stock_health";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ( $conn->connect_error ) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	$json = file_get_contents('foSecStockWatch.json');
	$json_decoded = json_decode( $json );

	//print "<pre>";
	//print_r( $json_decoded );

	$candlestick_pattern = '';
	$volume_percentage = '';
	$trend = '';

	foreach ( $json_decoded->data as $key => $value ) {
		$symbol = $value->symbol;
		$sql = "INSERT INTO $table (symbol, candlestick_pattern, volume_percentage, trend, executed) VALUES('".
		       $symbol . "', '" .
		       $candlestick_pattern . "', '" .
		       $volume_percentage . "', '" .
		       $trend . "',
			   NOW())";

		//print $sql . "\r\n";
		$conn->query($sql);
	}
}
