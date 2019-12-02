<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "surya";

//$symbol = trim($_GET['symbol']);
//$symbol = str_replace('_', '&', $symbol);
$symbol = 'RELIANCE';

print $symbol . "\r\n";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error || empty( $symbol )) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	$csv_raw = array_map( 'str_getcsv', file( 'MODIFIED/' . $symbol . '.csv' ) );
	print '<pre>';
	print_r( $csv_raw );
}
