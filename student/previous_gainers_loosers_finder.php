<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "daibik";
//$fo_gainers = "fo_gainers";
//$fo_loosers = "fo_loosers";

$symbol = trim($_GET['symbol']);
//$symbol = 'AJANTPHARM';

//print $symbol . " started\r\n";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error || empty( $symbol )) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	$csv = array_map( 'str_getcsv', file( '../ohl_stocks/MODIFIED/' . $symbol . '.csv' ) );

	//print '<pre>';
	//print_r($csv);

	$change_percentage = 0.00;
	for ( $i=0; $i<=(count($csv) - 2); $i++ ) {
		if ( $i > 0 ) {
			$last_traded_price = trim( $csv[$i][6] );
			$close_price = trim( $csv[$i+1][7] );
			$date_added = trim( $csv[$i][0] );

			if ( $last_traded_price >= $close_price ) {
                $table = "fo_gainers";
				$change_percentage = number_format(((($last_traded_price - $close_price)/$close_price)*100), 2, '.', '');
				$sql = "INSERT INTO " . $table ." (symbol, 
		                                       date_added, 
		                                       high_percentage,
		                                       executed)
									       VALUES('" . $symbol . "',
									              '" . date( 'Y-m-d', strtotime( $date_added ) ). "',
									              '" . $change_percentage. "',
									              NOW()
		                                )";
			}
			elseif ( $last_traded_price < $close_price ) {
				$table = "fo_loosers";
				$change_percentage = number_format(((($close_price - $last_traded_price)/$close_price)*100), 2, '.', '');
				$sql = "INSERT INTO " . $table ." (symbol, 
		                                       date_added, 
		                                       low_percentage,
		                                       executed)
									       VALUES('" . $symbol . "',
									              '" . date( 'Y-m-d', strtotime( $date_added ) ). "',
									              '" . $change_percentage. "',
									              NOW()
		                                )";
			}

			$conn->query($sql);
			print $symbol . " updated\r\n";
		}
	}
}
