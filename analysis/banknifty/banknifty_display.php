<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "surya";
$table = "live_banknifty";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error ) {
	die("Connection failed: " . $conn->connect_error);
}
else {

	$sql = "SELECT * FROM $table ";
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
<table border="1">
	<tr>
		<th>Current Price</th>
		<th>Difference With Price</th>
		<th>Difference</th>
		<th>Difference With Previous</th>
		<th>Executed</th>
	</tr>

	<?php
	$prev_difference = $prev_price = 0;
	foreach ( $data as $key => $value ) {
			?>
			<tr style="background-color:<?php //print $color; ?>">
				<td><?php print $value['last_price']; ?></td>
				<td><?php print ( $value['last_price'] - $prev_price ); ?></td>
				<td><?php print $value['difference']; ?></td>
				<td><?php print ($value['difference'] - $prev_difference ); ?></td>
				<td><?php print date( 'd-m-Y H:i:s', strtotime( $value['executed'] ) ); ?></td>
			</tr>
			<?php
		$prev_difference = $value['difference'];
		$prev_price = $value['last_price'];
	}
	?>
</table>
</body>
</html>
