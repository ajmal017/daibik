<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "daibik";
$table = "stocks_movement";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error ) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	$symbol = trim($_GET['symbol']);

	$sql = "SELECT difference, current_price, direction, executed FROM $table " . " WHERE symbol ='". $symbol . "'";
	$query=mysqli_query($conn,$sql);
	while ( $result = mysqli_fetch_array($query,MYSQLI_ASSOC) ) {
		$data[] = $result;
	}
}

/*print '<pre>';
print_r($data);
exit;*/
?>
<html>
<head>
	<title>Testing</title>
</head>
<body>
<h3><?php print $symbol; ?></h3>
<table border="1">
	<tr>
		<th>Current Price</th>
		<th>Difference</th>
		<th>Direction</th>
		<th>Executed</th>
	</tr>

	<?php
	$prev_price = $prev_direction = 0;
	$direction = $color = '';
        foreach ( $data as $key => $value ) {
	        if ( $prev_price > 0 && $prev_price != $value['current_price'] ) {
		        if ( $prev_direction == $value['direction'] &&
		              $value['direction'] == 2 ) {
			        if ( $prev_difference > $value['difference'] ) {
				        $direction = "Buyer Increases";
				        $color = "green";
			        }
			        else {
				        $direction = "Seller Increases";
				        $color = "red";
			        }
		        }

		        if ( $prev_direction == $value['direction'] &&
		             $value['direction'] == 1 ) {
			        if ( $prev_difference > $value['difference'] ) {
				        $direction = "Buyer Increases";
				        $color = "green";
			        }
			        else {
				        $direction = "Seller Increases";
				        $color = "red";
			        }
		        }

		        if ( $value['direction'] == 1 && $prev_direction == 2 ) {
			        $direction = "Buyer Increases(Crossover)";
			        $color = "green";
		        }

		        if ( $value['direction'] == 2 && $prev_direction == 1 ) {
			        $direction = "Seller Increases(Crossover)";
			        $color = "red";
		        }

		        ?>
		        <tr style="background-color:<?php //print $color; ?>">
			        <td><?php print $value['current_price']; ?></td>
			        <td><?php print $value['difference'] . '(' . $value['direction'] . ')'; ?></td>
			        <td><?php print $direction; ?></td>
			        <td><?php print date( 'd-m-Y H:i:s', strtotime( $value['executed'] ) ); ?></td>
		        </tr>
		        <?php
	        }
	        $prev_price = $value['current_price'];
	        $prev_direction = $value['direction'];
	        $prev_difference = $value['difference'];
	    }
	 ?>
</table>
</body>
</html>
