<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "daibik";
$table = "next_match";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error ) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	$sql = "SELECT * FROM $table WHERE total_ol_appeared=0 ORDER BY gap ASC";
	$query=mysqli_query($conn,$sql);
	while ( $result = mysqli_fetch_array($query,MYSQLI_ASSOC) ) {
		$data[] = $result;
	}

	//print '<pre>';
	//print_r($data);
	$json_encoded = json_encode($data);
	file_put_contents('ol_not_come_yet.json', $json_encoded);
}

?>
