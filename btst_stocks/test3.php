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
//$symbol = 'UNIONBANK';

print $symbol . "\r\n";
// Create connection
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ( $conn->connect_error ||  empty( $symbol )) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	$csv_raw = array_map( 'str_getcsv', file( 'MODIFIED/' . $symbol . '.csv' ) );

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

	$csv = array_reverse( $csv );

	//print "<pre>";
	//print_r($csv);

	$count = 0;
	if ( !empty( $csv ) ) {
		foreach ( $csv as $key => $value ) {
			$next_day_open_high_percentage = 0;
			if ( isset( $csv[$key+1] ) ) {
               if ( $csv[$key+1][1] < $csv[$key][1] && $csv[$key][1] > $csv[$key][4] && $csv[$key+1][1] > $csv[$key+1][4]) {
                 $count++;
               }
               else {
               	   if ( $count >= 2 && $csv[$key+1][1] < $csv[$key+1][4] ) {
               	   	 if ( isset( $csv[$key+2] )) {
	                     $next_day_open_high_percentage = number_format((($csv[$key+2][2] - $csv[$key+2][1])/$csv[$key+2][1])*100, 2, '.', '');
                     }
                     print $csv[$key+1][0] . " | Count = " . $count . " | Next Day OH = " . $next_day_open_high_percentage . "\r\n";
                   }
	               $count = 0;
               }
			}
		}

		foreach ( $csv as $key => $value ) {
			$next_day_open_low_percentage = 0;
			if ( isset( $csv[$key+1] ) ) {
				if ( $csv[$key+1][1] > $csv[$key][1] && $csv[$key][1] < $csv[$key][4] && $csv[$key+1][1] < $csv[$key+1][4]) {
						$count++;
				}
				else {
					if ( $count >= 2 && $csv[$key+1][1] > $csv[$key+1][4] ) {
						if ( isset( $csv[$key+2] )) {
							$next_day_open_low_percentage = number_format((($csv[$key+2][1] - $csv[$key+2][3])/$csv[$key+2][1])*100, 2, '.', '');
						}
						print $csv[$key+1][0] . " | Count = " . $count . " | Next Day OL = " . $next_day_open_low_percentage . "\r\n";
					}
					$count = 0;
				}
			}
		}
	}
}
