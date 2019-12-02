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
  $all_stocks = json_decode( file_get_contents("stocks.json") );
  //print '<pre>';
  //print_r($all_stocks);

  foreach ( $all_stocks as $key => $value ) {
	  $sql = "SELECT * FROM $table " . " WHERE symbol ='". $value->symbol . "'";
	  $query=mysqli_query($conn,$sql);
	  while ( $result = mysqli_fetch_array($query,MYSQLI_ASSOC) ) {
		  $list_stocks[$value->symbol][] = $result;
	  }
  }
}

//print '<pre>';
//print_r($list_stocks);
?>
<html>
<head>
	<title>Testing</title>
</head>
<body>
 <table border="1">
	<tr>
		<th>Symbol</th>
		<th>Current Price</th>
		<th>Count</th>
		<th>Direction</th>
	</tr>

	 <?php
	   if ( isset( $list_stocks ) && !empty( $list_stocks ) ) {
		   foreach ( $list_stocks as $symbol => $data ) {
			   $length        = count( $data );
			   $trend_count   = 0;
			   $current_price = $data[ $length - 1 ]['current_price'];
			   $direction     = $data[ $length - 1 ]['direction'];
			   for ( $i = ( $length - 1 ); $i > 0; $i -- ) {
				   if ( $data[ $i ]['difference'] >= $data[ $i - 1 ]['difference'] &&
				        $data[ $i ]['direction'] == $data[ $i - 1 ]['direction']
				   ) {
					   $trend_count ++;
				   } else {
					   break;
				   }
			   }
			   ?>
			   <tr style="background-color:<?php print $direction == 2 ? 'red' : 'green'; ?>">
				   <td>
					   <a href="http://localhost/daibik/stock_display_details.php?symbol=<?php print $symbol; ?>"
					      target="_blank"><?php print $symbol; ?></a></td>
				   <td><?php print $current_price; ?></td>
				   <td><?php print $trend_count; ?></td>
				   <td><?php print ( $direction == 2 ) ? 'Seller Increases' : 'Buyer Increases'; ?></td>
			   </tr>
			   <?php
		   }
	   }
	 ?>
 </table>
</body>
</html>
