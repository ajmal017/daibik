<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "daibik";
$table = "fo_all";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error ) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	//$data = json_decode(trim($_GET['data']));
	$data = json_decode(file_get_contents('foSecStockWatch.json'));

	/*print count($data->data);

	print '<pre>';
	print_r($data);*/

	if ( isset( $data->data ) && !empty( $data->data )) {
		foreach ( $data->data as $key => $value ) {
			$sql = "INSERT INTO " . $table ." (symbol, 
			                                   open, 
			                                   high, 
			                                   low, 
			                                   ltp, 
			                                   value_change, 
			                                   percentile_change, 
			                                   volume_in_lakhs, 
			                                   turnover, 
			                                   52_week_high, 
			                                   52_week_low, 
			                                   365_day_percentile_change,
			                                   30_day_percentile_change,
			                                   executed) 
		       VALUES('" . $value->symbol . "',
		              '" . $value->open . "',
		              '" . $value->high . "',
		              '" . $value->low . "',
		              '" . $value->ltP . "',
		              '" . $value->ptsC . "',
		              '" . $value->per . "',
		              '" . $value->trdVol . "',
		              '" . $value->ntP . "',
		              '" . $value->wkhi . "',
		              '" . $value->wklo . "',
		              '" . $value->yPC . "',
		              '" . $value->mPC . "',
		              NOW()
		              )";

			//print $sql . "<br />";
			$conn->query($sql);

			print $value->symbol . " added \r\n";
		}
	}
}
?>
