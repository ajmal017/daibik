<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "daibik";
$table = "positional";

$symbol = trim($_GET['symbol']);
$symbol = str_replace('_', '&', $symbol);
//$symbol = 'ACC';

//print $symbol . "\r\n";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error || empty( $symbol )) {
	die("Connection failed: " . $conn->connect_error);
}
else {

	$csv = array_map( 'str_getcsv', file( 'MODIFIED/' . $symbol . '.csv' ) );

	//print '<pre>';
	//print_r($csv);

	// Today's Price
	$open_price_today = trim( $csv[1][1] );
	$high_price_today = trim( $csv[1][2] );
	$low_price_today = trim( $csv[1][3] );
	$close_price_today = trim( $csv[1][4] );

	// Previous 3rd Day Price
	$open_price_prev_third_day = trim( $csv[4][1] );
	$high_price_today_prev_third_day = trim( $csv[4][2] );
	$low_price_today_prev_third_day = trim( $csv[4][3] );

	// Condition 1
	if ( $open_price_today < $close_price_today ) {
      // Condition 2
	  if ( $open_price_today > $low_price_today_prev_third_day ) {
		  // Condition 3
		  if ( $open_price_today < $high_price_today_prev_third_day && $high_price_today_prev_third_day < $high_price_today ) {
			  $target_buy_price = number_format(($high_price_today + .05), 2, '.', '');
			  $difference = $high_price_today - $low_price_today;
              $target_sell_price = $target_buy_price + $difference;

			  $sql = "INSERT INTO " . $table ." (symbol, targeted_buy_price, targeted_sell_price, difference, executed)
		       VALUES('" . $symbol . "',
		              '" . $target_buy_price . "',
		              '" . $target_sell_price . "',
		              '" . $difference . "',
		              NOW()
		              )";

			  $conn->query($sql);

			  echo "$symbol updated\r\n";
		  }
	  }
	}
}
?>
