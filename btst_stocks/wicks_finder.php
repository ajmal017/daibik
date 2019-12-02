<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
define('ACCEPTED_BODY_LENGTH_RATIO', 10);
define('ACCEPTED_WICKS_RATIO', 2);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "tojo";
$table = "btst_wicks";

$symbol = trim($_GET['symbol']);
$symbol = str_replace('_', '&', $symbol);
//$symbol = 'BPCL';

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
			$csv[$key][5] = trim( $value[8] ); //ltp
		}
	}

	//print '<pre>';
	//print_r($csv_raw);

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

	$output = $csv;

	$output = array_reverse( $output );
	//print '<pre>';
	//print_r($output);

	foreach ( $output as $key => $value ) {
		if ( $value[1] != $value[4] && ! empty( $output[$key] ) ) {
			$body = abs( $value[1] - $value[4] );
			$total_length = $value[2] - $value[3];
			$lower_wicks = 0;
			$upper_wicks = 0;
			$wicks_to_wicks = 0;
			$next_day_percentage = $next_day_percentage_open = $diff_volume_percentage = 0;
			$type = '';


			$body_to_length = number_format(( $total_length/$body), 2, '.', '');
			$low_high_percentage = number_format( ($total_length/ $value[3] )*100, 2, '.', '' );

			// Lower wicks(Bearish)
			if ( $value[1] > $value[4] ) {
				$lower_wicks = $value[4] - $value[3];
			}
			elseif ( $value[1] < $value[4] ) { // (Bullish)
				$lower_wicks = $value[1] - $value[3];
			}

			// Upper wicks(Bearish)
			if ( $value[1] > $value[4] ) {
				$upper_wicks = $value[2] - $value[1];
			}
			elseif ( $value[1] < $value[4] ) { // (Bullish)
				$upper_wicks = $value[2] - $value[4];
			}

			//print 'Divison by zero ' . $value[0] . ' ==  '  . $upper_wicks . "<br />";

			//if ( $lower_wicks > $upper_wicks && $upper_wicks > 0 ) {
			if ( $lower_wicks > $upper_wicks ) {
				if ( $upper_wicks > 0 ) {
					$wicks_to_wicks = number_format(( $lower_wicks/$upper_wicks), 2, '.', '');
				}
				else {
					$wicks_to_wicks = 0;
				}
				$type = 'bullish';
			}
			//elseif ( $lower_wicks < $upper_wicks && $lower_wicks > 0 ) {
			elseif ( $lower_wicks < $upper_wicks ) {
				if ( $lower_wicks > 0 ) {
					$wicks_to_wicks = number_format(( $upper_wicks/$lower_wicks), 2, '.', '');
				}
				else {
					$wicks_to_wicks = 0;
				}

				$type = 'bearish';
			}
			else {
				$wicks_to_wicks = 1;
				$type = 'none';
			}

			//print $value[0] . '   div =' . (($value[2] - $value[3])/($value[1] - $value[4])) . '  '  . ($value[2] - $value[3]) . '   ' . ($value[1] - $value[4]) . '   ' . $body_to_length . '   ' . $wicks_to_wicks . "<br />";

			//if ( ( $body_to_length > ACCEPTED_BODY_LENGTH_RATIO ) && ( $wicks_to_wicks > ACCEPTED_WICKS_RATIO ) ) {
			if ( ( $body_to_length > ACCEPTED_BODY_LENGTH_RATIO ) ) {

				if ( $type === 'bullish' && ! empty( $output[$key+1][2] )) {
					$next_day_percentage = number_format( (($output[$key+1][2] - $output[$key][4])/$output[$key][4])*100, 2, '.', '');
					$next_day_percentage_open = number_format( (($output[$key+1][2] - $output[$key+1][1])/$output[$key+1][1])*100, 2, '.', '');
				}
				elseif ( $type === 'bearish' && !empty( $output[$key+1][3] )) {
					$next_day_percentage = number_format( (($output[$key][4] - $output[$key+1][3])/$output[$key][4])*100, 2, '.', '');
					$next_day_percentage_open = number_format( (($output[$key+1][1] - $output[$key+1][3])/$output[$key+1][1])*100, 2, '.', '');
				}

				/*print $key . "<br />";
                print "<pre>";
                print_r( $csv[$key+1] );*/
                //print $next_day_percentage . "<br />";

                if ( !empty( $output[$key-1][5] ) ) {
                    $diff_volume_percentage = number_format((($output[$key][5] - $output[$key-1][5])/$output[$key-1][5])*100, 2, '.', '');
                }

				$sql = "INSERT INTO `btst_wicks` (symbol, body_to_length, low_high_percentage, volume, next_day_percentage_open, wicks_to_wicks, next_day_percentage, `type`, executed) VALUES('".
				       $symbol . "', " .
				       $body_to_length . ", " .
				       $low_high_percentage . ", " .
				       $diff_volume_percentage . ", " .
				       $next_day_percentage_open . ", " .
				       $wicks_to_wicks . ", " .
				       $next_day_percentage . ", '" .
				       $type . "', '" .
				       date('Y-m-d', strtotime( $value[0] ) ). "')";

				//print $sql . "\r\n";
				//print $symbol . "\r\n";

				$conn->query($sql);
			}
		}
	}
}
