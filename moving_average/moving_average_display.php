<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('FIVE_DAYS_PERIOD', 5);
define('FIFTEEN_DAYS_PERIOD', 15);
define('SMOOTHNING_CONSTANT_FIVE_DAYS_PERIOD', 2/(FIVE_DAYS_PERIOD + 1));
define('SMOOTHNING_CONSTANT_FIFTEEN_DAYS_PERIOD', 2/(FIFTEEN_DAYS_PERIOD + 1));

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "daibik";
$table = "moving_averages";
$store_data = array();

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error ) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	$sql = "SELECT DISTINCT(symbol) FROM moving_averages";
	$query=mysqli_query($conn,$sql);
	while ( $result = mysqli_fetch_array($query,MYSQLI_ASSOC) ) {
		$sql_display = "SELECT symbol, difference FROM 	moving_averages where symbol ='". $result['symbol'] ."' order by date_excecuted desc LIMIT 0,1";
		$query_display =mysqli_query($conn,$sql_display);
		while ( $result_data = mysqli_fetch_array($query_display,MYSQLI_ASSOC) ) {
			//print '<pre>';
			//print_r($result_data);
			$store_data[] = $result_data;
		}
	}
	
	for ( $i = 0; $i < count($store_data) - 1; $i++ ) {
		$min_index = $i;
		for ( $j = $i +1 ; $j < count($store_data); $j++ ) {
			if ( $store_data[$j]['difference'] < $store_data[$min_index]['difference'] ) {
				$min_index = $j;
			}
		}
        $temp_store = $store_data[$i];
		$store_data[$i] = $store_data[$min_index];
		$store_data[$min_index] = $temp_store;
	}
}
?>
<html>
<head>
	<title>Testing</title>
</head>
<body>
</body>
<table border="1">
	<tr>
		<th>Symbol</th>
		<th>Difference</th>
	</tr>
	<?php
	  foreach ( $store_data as $key => $value ) {
	?>
	<tr>
		<td><a href="http://localhost/daibik/moving_average_display_details.php?symbol=<?php print $value['symbol']; ?>"><?php print $value['symbol']; ?></a></td>
		<td><?php print $value['difference']; ?></td>
	</tr>
	<?php
	  }
	?>
</table>
</html>
