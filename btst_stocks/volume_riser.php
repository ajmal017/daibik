<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "tojo";
$table = "volume_riser";

$symbol = trim($_GET['symbol']);
$symbol = str_replace('_', '&', $symbol);
//$symbol = 'SUNTV';

print $symbol . "\r\n";
// Create connection
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ( $conn->connect_error ||  empty( $symbol )) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	$csv_raw = array_map( 'str_getcsv', file( 'MODIFIED/' . $symbol . '.csv' ) );

	//print '<pre>';
	//print_r($csv_raw);

	$csv = $info = $data = array();

	foreach ( $csv_raw as $key => $value ) {
		if ( !empty( $value[0] ) && $key > 0 ) {
			$csv[$key][0] = $value[0];
			$csv[$key][1] = trim( $value[3] ); //open
			$csv[$key][2] = trim( $value[4] ); //high
			$csv[$key][3] = trim( $value[5] ); //low
			$csv[$key][4] = trim( $value[7] ); //close
			$csv[$key][5] = trim( $value[8] ); //Total Traded Quantity
		}
	}

	//print '<pre>';
	//print_r($csv);
	$average_volume = $total_volume = $highest_volume = $prev_highest_volume = 0;
	$percentage_average = $percentage_highest = 0;
	$count = 1;
	if ( !empty( $csv ) ) {
		foreach ( $csv as $key => $value ) {
			if ( $count == 1 ) {
				$highest_volume = $value[5];
			}
			else {
				if ( $value[5] > $prev_highest_volume ) {
					$highest_volume = $value[5];
				}
			}

			if ( $count > 10 ) {
				$average_volume = number_format(( $total_volume / 10 ),2, '.', '' );
				break;
			}
			$total_volume = $total_volume + $value[5];
			$prev_highest_volume = $highest_volume;
			$count++;
		}
	}

	//print 'average_volume = ' . $average_volume;

	$json = file_get_contents('downloads/' . $symbol . '.json');
	$json_decoded = json_decode( $json );

	//print '<pre>';
	//print_r($json_decoded);

	if ( !empty( $json_decoded->data[0] ) ) {
		$date = date('d-M-Y');
		$open_price = str_replace( ',', '', $json_decoded->data[0]->open );
		$high_price = str_replace( ',', '', $json_decoded->data[0]->dayHigh );
		$low_price = str_replace( ',', '', $json_decoded->data[0]->dayLow );
		$close_price = str_replace( ',', '', $json_decoded->data[0]->lastPrice );
		$volume = str_replace(',', '', $json_decoded->data[0]->totalTradedVolume);

		if ( ( $volume > $average_volume ) ) {
			$percentage_average = number_format((( $volume - $average_volume )/$average_volume), 2, '', '');
			$percentage_highest = number_format((( $volume - $highest_volume )/$highest_volume), 2, '', '');
			print $symbol . "===" . $percentage_average . "\r\n";

			$sql = "INSERT INTO $table (symbol, percentage_average, percentage_highest, executed) VALUES('".
			       $symbol . "', " .
			       $percentage_average . ", " .
			       $percentage_highest . ",
			       NOW())";

			$conn->query($sql);
		}
	}
}
