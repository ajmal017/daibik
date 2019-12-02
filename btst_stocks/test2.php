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
//$symbol = 'BANKBARODA';

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

	/*$json = file_get_contents('downloads/' . $symbol . '.json');
	$json_decoded = json_decode( $json );

	$date = date('d-M-Y');
	$open_price = str_replace( ',', '', $json_decoded->data[0]->open );
	$high_price = str_replace( ',', '', $json_decoded->data[0]->dayHigh );
	$low_price = str_replace( ',', '', $json_decoded->data[0]->dayLow );
	$close_price = str_replace( ',', '', $json_decoded->data[0]->lastPrice );
	$volume = str_replace(',', '', $json_decoded->data[0]->totalTradedVolume);

	$output_latest = [
		1 => [
			0 => $date,
			1 => $open_price,
			2 => $high_price,
			3 => $low_price,
			4 => $close_price,
			5 => $volume,
		]
	];

	$output = array_merge($output_latest, $csv);*/

	$csv = array_reverse( $csv );

	//print "<pre>";
	//print_r($csv);

	if ( !empty( $csv ) ) {
		$count = 1;
		$match = $pattern_v_p_reverse = 0;
		foreach ( $csv as $key => $value ) {
			$OL = number_format(((( $csv[$key][1] - $csv[$key][3] )/$csv[$key][1] )*100), 2, '.', '' );
			$HO = number_format(((($csv[$key][2] - $csv[$key][1] )/$csv[$key][2])*100), 2, '.', '');
			//print $count . ". Date = " . $value[0] . "   OL = " . $OL . "    HO = " . $HO . " Volume = " . $value[5] . "\r\n";
			//print $count . ". Date = " . $value[0] . "   CP = " . $value[4] . " Volume = " . $value[5] . "\r\n";
			$count++;

			if ( isset( $csv[$key+2] ) ) {
				$diff_close_price = $csv[$key+1][4] - $csv[$key][4];
				$diff_volume      = $csv[$key+1][5] - $csv[$key][5];
				$diff_price = $diff_close_price_percentage = $diff_volume_percentage = 0;
				$diff_high_open_price_percentage = $diff_open_low_price_percentage = 0;

				/*if ( isset( $csv[$key+2] ) ) {
					$diff_high_open_price_percentage = number_format((abs($csv[$key+2][1] - $csv[$key+2][2])/$csv[$key+2][1])*100, 2, '.', '');
					$diff_open_low_price_percentage = number_format((abs($csv[$key+2][1] - $csv[$key+2][3])/$csv[$key+2][1])*100, 2, '.', '');

					$diff_volume_percentage = number_format((($csv[$key+1][5] - $csv[$key][5])/$csv[$key][5])*100, 2, '.', '');
					$diff_close_price_percentage = number_format((($csv[$key+1][4] - $csv[$key][4])/$csv[$key][4])*100, 2, '.', '');

					if ( $diff_volume_percentage >= 50) {
						print $csv[ $key + 2 ][0] . " Difference Between CP = " . $diff_close_price_percentage .  "  Volume = " . $diff_volume_percentage . " Difference Between High and open = " .$diff_high_open_price_percentage . " Difference Between open and low =  " . $diff_open_low_price_percentage . "\r\n";
					}
				}*/

				if ( isset( $csv[$key+1] ) && isset( $csv[$key-1] ) ) {
					$diff_high_open_price_percentage = number_format((abs($csv[$key+1][1] - $csv[$key+1][2])/$csv[$key+1][1])*100, 2, '.', '');
					$diff_open_low_price_percentage = number_format((abs($csv[$key+1][1] - $csv[$key+1][3])/$csv[$key+1][1])*100, 2, '.', '');

					$diff_volume_percentage = number_format((($csv[$key][5] - $csv[$key-1][5])/$csv[$key-1][5])*100, 2, '.', '');
					$diff_close_price_percentage = number_format((($csv[$key][4] - $csv[$key-1][4])/$csv[$key-1][4])*100, 2, '.', '');

                    if ( ( $csv[$key][1] === $csv[$key][2] )  ) {
	                    print $csv[ $key + 1 ][0] . " Difference Between CP = " . $diff_close_price_percentage . "  Volume = " . $diff_volume_percentage . " Difference Between High and open = " .$diff_high_open_price_percentage . " Difference Between open and low =  " . $diff_open_low_price_percentage . "  Open-high" . "\r\n";
                    }

					if ( $csv[$key][1] === $csv[$key][3] ) {
						print $csv[ $key + 1 ][0] . " Difference Between CP = " . $diff_close_price_percentage . "  Volume = " . $diff_volume_percentage . " Difference Between High and open = " .$diff_high_open_price_percentage . " Difference Between open and low =  " . $diff_open_low_price_percentage . "  Open-low" . "\r\n";
					}
				}
			}
		}
	}
}
