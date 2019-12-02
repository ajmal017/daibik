<?php
/**
 * Reverse Direction.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "tojo";
$table = "volume_riser";

$symbol = trim($_GET['symbol']);
$symbol = str_replace('_', '&', $symbol);
//$symbol = 'KAJARIACER';

print $symbol . "\r\n";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ( $conn->connect_error ||  empty( $symbol )) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	$csv_raw = array_map( 'str_getcsv', file( 'MODIFIED/' . $symbol . '.csv' ) );

	$csv = $info = $data = array();

	foreach ( $csv_raw as $key => $value ) {
		if ( ! empty( $value[0] ) && $key > 0 ) {
			$csv[ $key ][0] = $value[0];
			$csv[ $key ][1] = trim( $value[3] ); //open
			$csv[ $key ][2] = trim( $value[4] ); //high
			$csv[ $key ][3] = trim( $value[5] ); //low
			$csv[ $key ][4] = trim( $value[7] ); //close
			$csv[ $key ][5] = trim( $value[8] ); //Total Traded Quantity
		}
	}

	$csv = array_reverse( $csv );

	//print "<pre>";
	//print_r( $csv );

	if ( !empty( $csv ) ) {
		foreach ( $csv as $key => $value ) {
			if ( $value[1] != $value[4] && isset( $csv[$key] ) ) {
				$body = abs( $value[1] - $value[4] );
				$total_length = $value[2] - $value[3];
				$body_to_length = number_format(( $total_length/$body), 2, '.', '');
				$upper_wicks = $lower_wicks = $upper_wicks_body = 0;

				if ( isset( $csv[$key-1][5] ) ) {
					$diff_volume_percentage = number_format((($csv[$key][5] - $csv[$key-1][5])/$csv[$key-1][5])*100, 2, '.', '');
				}
				else {
					$diff_volume_percentage = 0;
				}

				// Open Low
				if ( $value[1] == $value[3] ) {
					$upper_wicks = $value[2] - $value[4];
					$upper_wicks_body = number_format( ( $upper_wicks/$body ), 2, '.', '');
					if ( $upper_wicks > $body && $diff_volume_percentage >= 20 && isset( $csv[$key+1] )) {
						$next_day_percentage_open = number_format( (($csv[$key+1][1] - $csv[$key+1][3])/$csv[$key+1][3])*100, 2, '.', '');
						if ( $next_day_percentage_open < 1 ) {
							//print $csv[$key][0] . " | Ratio = " . $upper_wicks_body. " | OL = " . $next_day_percentage_open . " | Volume = " . $diff_volume_percentage .  "\r\n";
						}
					}
					else {
						if ( $csv[$key][0] == '01-Mar-19' ) {
							print $csv[$key][0] . " | Ratio = " . $upper_wicks_body. " | Volume = " . $diff_volume_percentage .  "\r\n";
						}
					}
				}

				// Open High
				if ( $value[1] == $value[2] ) {
					$lower_wicks = $value[4] - $value[3];
					$lower_wicks_body = number_format( ( $lower_wicks/$body ), 2, '.', '');
					if ( $lower_wicks > $body && $diff_volume_percentage >= 20 && isset( $csv[$key+1] )) {
						$next_day_percentage_open = number_format( (($csv[$key+1][2] - $csv[$key+1][1])/$csv[$key+1][1])*100, 2, '.', '');
						if ( $next_day_percentage_open < 1 ) {
							//print $csv[$key][0] . "| Ratio = " . $lower_wicks_body . "  | HO = " . $next_day_percentage_open . " | Volume = " . $diff_volume_percentage . "\r\n";
						}
					}
					else {
						if ( $csv[$key][0] == '01-Mar-19' ) {
							print $csv[$key][0] . "| Ratio = " . $lower_wicks_body . " | Volume = " . $diff_volume_percentage . "\r\n";
						}
					}
				}
			}
		}
	}
}
