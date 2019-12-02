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
	$symbol = trim($_GET['symbol']);
	print '<h2>' . $symbol . '</h2><br />';

	$sql = "SELECT * FROM moving_averages WHERE symbol='" . $symbol . "'";
	$query=mysqli_query($conn,$sql);
    while ( $result = mysqli_fetch_array($query,MYSQLI_ASSOC) ) {
	    $data[] = $result;
    }

	//print '<pre>';
	//print_r($data);
}
?>
<html>
<head>
	<title>Testing</title>
</head>
<body>
<table border="1">
	<tr>
		<th>Count</th>
		<th>Date</th>
		<th>Closing Price</th>
		<!--<th><?php //print FIVE_DAYS_PERIOD; ?> Day SMA</th>-->
		<th><?php print FIVE_DAYS_PERIOD; ?> Day EMA</th>
		<!--<th><?php //print FIFTEEN_DAYS_PERIOD; ?> Day SMA</th>-->
		<th><?php print FIFTEEN_DAYS_PERIOD; ?> Day EMA</th>
		<th>Difference</th>
	</tr>
	<?php
	foreach ( $data as $key => $value ) {
		?>
		<tr>
			<td><?php print $key + 1; ?></td>
			<td><?php print date('d-m-Y', strtotime( $value['date_excecuted'] )); ?></td>
			<td><?php print $value['closing_price']; ?></td>
			<!--<td><?php //print $value['simple_moving_average_five_day']; ?></td>-->
			<td><?php print $value['five_day_ema']; ?></td>
			<!--<td><?php //print $value['simple_moving_average_fifteen_day']; ?></td>-->
			<td><?php print $value['fifteen_day_ema']; ?></td>
			<td><?php print $value['difference']; ?></td>
		</tr>
		<?php
	}
	?>
</table>
</body>
</html>
