<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "surya";
$table = "live_feed_stocks";

$symbol = trim($_GET['symbol']);
$symbol = str_replace('_', '&', $symbol);
//$symbol = 'JINDALSTEL';

//print $symbol . "\r\n";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error || empty( $symbol )) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	$json = file_get_contents('downloads/' . $symbol . '.json');

	$csv_raw = array_map( 'str_getcsv', file( 'MODIFIED/' . $symbol . '.csv' ) );

	$csv = $info = array();
	foreach ( $csv_raw as $key => $value ) {
		$csv[$key][0] = trim( $value[0] );
		$csv[$key][1] = trim( $value[1] );
		$csv[$key][2] = trim( $value[2] );
		$csv[$key][3] = trim( $value[3] );
		$csv[$key][4] = trim( $value[4] );
		$csv[$key][5] = trim( $value[6] );
	}

	$data = json_decode( $json );

	$output_1 = array_slice($csv, 0, 1);
	$output_latest = [
		1 => [
			0 => date('d-M-Y'),
			1 => str_replace( ',', '', trim( $data->data[0]->openPrice ) ),
			2 => str_replace( ',', '', trim( $data->data[0]->highPrice ) ),
			3 => str_replace( ',', '', trim( $data->data[0]->lowPrice ) ),
			4 => str_replace( ',', '', trim( $data->data[0]->lastPrice ) ),
			5 => str_replace( ',', '', trim( $data->data[0]->numberOfContractsTraded ) ),
		]
	];
	$output_2 = array_slice($csv, 1);

	$output = array_merge($output_1,$output_latest, $output_2);

	$current_candlestick_pattern = patterMatch($output);

	/*print '<pre>';
	print_r( $output );*/
	/*exit;*/

	//$output = $csv;
	$total_length = count($output);

	//print "Buy Side \r\n";

	$low = 0;
	$low_key = $next_day_high_open = 0;
	foreach ( $output as $key => $value ) {
		if ( $key == 1 ) {
			$low = trim( $value[3] );
			$low_key = 1;
		}

		if ( $key > 1 && $low >  trim( $value[3] ) ) {
			$low = trim( $value[3] );
			$low_key = $key;
		}
	}

	//Average Volume Caluclation
	$average_volume = $total_percentage_volume = 0;
	if ( ( $low_key + 10 ) <= ( $total_length - 1 ) ) {
		for ( $ave_vol = $low_key; $ave_vol <= $low_key + 10; $ave_vol++ ) {
			if ( $ave_vol == $low_key ) {
				$current_volume = trim( $output[ $ave_vol ][5] );
			}
			$average_volume = $average_volume + trim( $output[ $ave_vol ][5] );
		}
		$average_volume = number_format( ( $average_volume / 10 ), 2, '.', '' );
		$total_percentage_volume = number_format( ( ( ( $current_volume - $average_volume ) / $average_volume ) * 100 ), 2, '.', '' );
	}

	if ( ($low_key + 3) < $total_length ) {
		if ( trim( $output[$low_key][4] ) > trim( $output[$low_key+1][4] ) &&
		     trim( $output[$low_key+1][4] ) < trim( $output[$low_key+2][4] ) &&
		     trim( $output[$low_key+2][4] ) < trim( $output[$low_key+3][4] )
		) {
			$percentage_increase = number_format(((trim( $output[$low_key][4] ) - trim( $output[$low_key+1][4] ) )/trim( $output[$low_key+1][4] ))*100, 2, '.', '');
			$low_close_percentage = number_format(((trim( $output[$low_key][4] ) - trim( $output[$low_key][3] ) )/trim( $output[$low_key][3] ))*100, 2, '.', '');
			if ( isset( $output[$low_key-1][1] ) && !empty( $output[$low_key-1][1] ) && trim( $output[$low_key-1][1] ) > 0 ) {
				$next_day_high_open = number_format(((trim( $output[$low_key-1][2] ) - trim( $output[$low_key-1][1] ) )/trim( $output[$low_key-1][1] ))*100, 2, '.', '');
			}
			//if ( $next_day_high_open <= 0.5 ) {
			if ( $output[$low_key][0] == '26-Sep-2018') {
				print 'Buy Side ' . $symbol . '  ' . $output[$low_key][0] . '   '. $percentage_increase. '    ' . $low_close_percentage . '  ' . $next_day_high_open . '   ' . $total_percentage_volume . '  ' . $current_candlestick_pattern . "\r\n";
			}
		  //}
		}
	}

	//print "Buy Side Ends\r\n";
	//print "Sell Side Starts\r\n";

	$high = 0;
	$high_key = $next_day_open_low = 0;
	foreach ( $output as $key => $value ) {
		if ( $key == 1 ) {
			$high = trim( $value[2] );
			$high_key = 1;
		}

		if ( $key > 1 && $high <  trim( $value[2] ) ) {
			$high = trim( $value[2] );
			$high_key = $key;
		}
	}

    //print 'high Key = ' . $high_key;
	//Average Volume Caluclation
	$average_volume = $total_percentage_volume = 0;
	if ( ( $high_key + 10 ) <= ( $total_length - 1 ) ) {
		for ( $ave_vol = $high_key; $ave_vol <= $high_key + 10; $ave_vol++ ) {
			if ( $ave_vol == $high_key ) {
				$current_volume = trim( $output[ $ave_vol ][5] );
			}
			$average_volume = $average_volume + trim( $output[ $ave_vol ][5] );
		}
		$average_volume = number_format( ( $average_volume / 10 ), 2, '.', '' );
		$total_percentage_volume = number_format( ( ( ( $current_volume - $average_volume ) / $average_volume ) * 100 ), 2, '.', '' );
	}

	if ( ($high_key + 3) < $total_length ) {
		if ( trim( $output[$high_key][4] ) < trim( $output[$high_key+1][4] ) &&
		     trim( $output[$high_key+1][4] ) > trim( $output[$high_key+2][4] ) &&
		     trim( $output[$high_key+2][4] ) > trim( $output[$high_key+3][4] )
		) {
			$percentage_decrease = number_format(((trim( $output[$high_key][4] ) - trim( $output[$high_key+1][4] ) )/trim( $output[$high_key+1][4] ))*100, 2, '.', '');
			$high_close_percentage = number_format(((trim( $output[$high_key][4] ) - trim( $output[$high_key][2] ) )/trim( $output[$high_key][2] ))*100, 2, '.', '');
			if ( isset( $output[$high_key-1][1] ) && !empty( $output[$high_key-1][1] ) && trim( $output[$high_key-1][1] ) > 0 ) {
				$next_day_open_low = number_format(((trim( $output[$high_key-1][3] ) - trim( $output[$high_key-1][1] ) )/trim( $output[$high_key-1][1] ))*100, 2, '.', '');
			}

			//if ( $next_day_open_low < 0.5 ) {
			 if ( $output[$high_key][0] == '26-Sep-2018') {
				 print 'Sell Side ' . $symbol . '  ' . $output[$high_key][0] . '   '. $percentage_decrease. '    ' . $high_close_percentage . '   ' . $next_day_open_low . '   ' . $total_percentage_volume . '  ' . $current_candlestick_pattern . "\r\n";
			 }
		   //}
		}
	}

	//print "Sell Side Ends\r\n";
}

function patterMatch($csv) {
	if ( !empty( $csv ) ) {
		foreach ( $csv as $key => $value ) {
			if ( $key > 0 && $key <= 1 ) {
				$date = date('Y-m-d', strtotime(trim( $value[0])));
				$open_price = trim( $value[1]);
				$high_price = trim( $value[2]);
				$low_price = trim( $value[3]);
				$close_price = trim( $value[4]);
				$total_length_data = count($csv) - 1;
				$pattern = '';

				$total_body_length = $high_price - $low_price;
				/************* 1.Calculation of Bullish Pin Bar.***********************/
				if ( $open_price <= $close_price ) {
					$length_lower_shadow = $open_price - $low_price;
				}
				else {
					$length_lower_shadow = $close_price - $low_price;
				}
				$percentage_length = number_format((($length_lower_shadow/$total_body_length)*100), 2, '.', '');

				// Check whether the length of lower wick is at least two third of total length.
				if ( $percentage_length >= 66.66 && ($key + 3) <= $total_length_data) {
					$pattern .= 'Paper Umbrella ';
				}
				/************* 1.Calculation of Bullish Pin Bar.***********************/

				/************* 2.Calculation of Bearish Pin Bar.***********************/
				if ( $open_price <= $close_price ) {
					$length_upper_shadow = $high_price - $close_price;
				}
				else {
					$length_upper_shadow = $high_price - $open_price;
				}
				$bearish_percentage_length = number_format((($length_upper_shadow/$total_body_length)*100), 2, '.', '');

				// Check whether the length of upper wick is at least two third of total length.
				if ( $bearish_percentage_length >= 66.66 && ($key + 3) <= $total_length_data) {
					$pattern .= 'Shooting Star ';
				}
				/************* 2.Calculation of Bearish Pin Bar.***********************/

				/************* 3.Calculation of Bullish One White Soldier.***********************/
				if ( $open_price <= $close_price &&
				     ($key + 1) <= $total_length_data &&
				     trim($csv[$key+1][1]) >= trim($csv[$key+1][4]) &&
				     //trim($csv[$key+2][1]) >= trim($csv[$key+2][4]) &&
				     //trim($csv[$key+3][1]) >= trim($csv[$key+3][4]) &&
				     $open_price > trim($csv[$key+1][4]) &&
				     $close_price > trim($csv[$key+1][1])
					//&& $low_price >= trim($csv[$key+1][3]) &&
					//$high_price >= trim($csv[$key+1][2])
				) {
					$pattern .= 'Bullish One White Soldier ';
				}

				/************* 3.Calculation of Bullish One White Soldier.***********************/

				/************* 4.Calculation of Bearish One Black Crow.***********************/
				if ( $open_price >= $close_price &&
				     ($key + 1) <= $total_length_data &&
				     trim($csv[$key+1][1]) <= trim($csv[$key+1][4]) &&
				     //trim($csv[$key+2][1]) <= trim($csv[$key+2][4]) &&
				     //trim($csv[$key+3][1]) <= trim($csv[$key+3][4]) &&
				     $open_price < trim($csv[$key+1][4]) &&
				     $close_price < trim($csv[$key+1][1])
					//&& $low_price <= trim($csv[$key+1][3]) &&
					//$high_price <= trim($csv[$key+1][2])

				) {
					$pattern .= 'Bearish One Black Crow ';
				}

				/************* 4.Calculation of Bearish One Black Crow.***********************/

				/************* 5.Calculation of Bullish Engulfing Pattern.***********************/
				if ( $open_price < $close_price &&
				     ($key + 1) <= $total_length_data &&
				     trim($csv[$key+1][1]) >= trim($csv[$key+1][4]) &&
				     $close_price > trim($csv[$key+1][1]) &&
				     $open_price < trim($csv[$key+1][4])
				) {
					$pattern .= 'Bullish Engulfing Pattern ';
				}
				/*************  5.Calculation of Bullish Engulfing Pattern.***********************/

				/************* 6.Calculation of Bearish Engulfing Pattern.***********************/
				if ( $open_price > $close_price &&
				     ($key + 1) <= $total_length_data &&
				     trim($csv[$key+1][1]) <= trim($csv[$key+1][4]) &&
				     $close_price < trim($csv[$key+1][1]) &&
				     $open_price > trim($csv[$key+1][4])
				) {
					$pattern .= 'Bearish Engulfing Pattern ';
				}

				/************* 6.Calculation of Bearish Engulfing Pattern.***********************/

				/************* 7.Calculation of Bullish Morning Star Pattern.***********************/
				if ( $open_price < $close_price &&
				     ($key + 2) <= $total_length_data &&
				     trim($csv[$key+2][1]) > trim($csv[$key+2][4])
				) {
					$size_first_candle = $close_price - $open_price;
					$size_second_candle = abs(trim($csv[$key+1][4]) - trim($csv[$key+1][1]));
					$size_third_candle = trim($csv[$key+2][1]) - trim($csv[$key+2][4]);
					$percentage_length_first_second = number_format((($size_second_candle/$size_first_candle)*100), 2, '.', '');
					$percentage_length_third_second = number_format((($size_second_candle/$size_third_candle)*100), 2, '.', '');

					if ( $percentage_length_first_second <= 33.33 &&
					     $percentage_length_third_second <= 33.33
					) {
						// If the second indecision candle is green
						if ( trim($csv[$key+1][1]) < trim($csv[$key+1][4]) ) {
							if ( $open_price > trim($csv[$key+1][4]) &&
							     trim($csv[$key+2][4]) > trim($csv[$key+1][4])
							) {
								$pattern .= 'Bullish Morning Star Pattern ';
							}
						}
						else {
							if ( $open_price > trim($csv[$key+1][1]) &&
							     trim($csv[$key+2][4]) > trim($csv[$key+1][1])
							) {
								$pattern .= 'Bullish Morning Star Pattern ';
							}
						}
					}
				}
				/************* 7.Calculation of Bullish Morning Star Pattern.***********************/

				/************* 8.Calculation of Bearish Evening Star Pattern.***********************/
				if ( $open_price > $close_price &&
				     ($key + 2) <= $total_length_data &&
				     trim($csv[$key+2][1]) < trim($csv[$key+2][4])
				) {
					$size_first_candle = $open_price - $close_price;
					$size_second_candle = abs(trim($csv[$key+1][4]) - trim($csv[$key+1][1]));
					$size_third_candle = trim($csv[$key+2][4]) - trim($csv[$key+2][1]);
					$percentage_length_first_second = number_format((($size_second_candle/$size_first_candle)*100), 2, '.', '');
					$percentage_length_third_second = number_format((($size_second_candle/$size_third_candle)*100), 2, '.', '');

					if ( $percentage_length_first_second <= 33.33 &&
					     $percentage_length_third_second <= 33.33
					) {
						// If the second indecision candle is green
						if ( trim($csv[$key+1][1]) < trim($csv[$key+1][4]) ) {
							if ( $open_price < trim($csv[$key+1][1]) &&
							     trim($csv[$key+2][4]) < trim($csv[$key+1][1])
							) {
								$pattern .= 'Bearish Evening Star Pattern ';
							}
						}
						else {
							if ( $open_price < trim($csv[$key+1][4]) &&
							     trim($csv[$key+2][4]) < trim($csv[$key+1][4])
							) {
								$pattern .= 'Bearish Evening Star Pattern ';
							}
						}
					}
				}

				/************* 8.Calculation of Bearish Evening Star Pattern.***********************/

				/************* 9.Calculation of Bullish Harami Pattern.***********************/
				if ( ($key + 1) <= $total_length_data &&
				     $open_price < $close_price &&
				     trim($csv[$key+1][1]) > trim($csv[$key+1][4]) &&
				     $open_price > trim($csv[$key+1][4]) &&
				     $close_price < trim($csv[$key+1][1])
				) {
					$pattern .= 'Bullish Harami Pattern ';
				}
				/************* 9.Calculation of Bullish Harami Pattern.***********************/

				/************* 10.Calculation of Bearish Harami Pattern.***********************/
				if ( ($key + 1) <= $total_length_data &&
				     $open_price > $close_price &&
				     trim($csv[$key+1][1]) < trim($csv[$key+1][4]) &&
				     $open_price < trim($csv[$key+1][4]) &&
				     $close_price > trim($csv[$key+1][1])
				) {
					$pattern .= 'Bearish Harami Pattern ';
				}
				/************* 10.Calculation of Bearish Harami Pattern.***********************/

				/************* 11.Calculation of Bullish Piercing Pattern.***********************/
				if ( ($key + 1) <= $total_length_data &&
				     $open_price < $close_price &&
				     trim($csv[$key+1][1]) > trim($csv[$key+1][4])
				) {
					$range_current = $close_price - $open_price;
					$range_previous =  trim($csv[$key+1][1]) - trim($csv[$key+1][4]);
					$range_ratio = number_format((($range_current/$range_previous)*100), 2, '.', '');
					if ( $range_ratio >= 20 && $range_ratio <= 100 ) {

						$pattern .= 'Piercing Pattern ';
					}
				}
				/************* 11.Calculation of Bullish Piercing Pattern.***********************/

				/************* 12.Calculation of Bearish Dark Cloud Cover Pattern.***********************/
				if ( ($key + 1) <= $total_length_data &&
				     $open_price > $close_price &&
				     trim($csv[$key+1][1]) < trim($csv[$key+1][4])
				) {
					$range_current = $close_price - $open_price;
					$range_previous = $open_price - $close_price;
					$range_ratio = number_format((($range_current/$range_previous)*100), 2, '.', '');
					if ( $range_ratio >= 50 && $range_ratio <= 100 ) {

						$pattern .= 'Dark Cloud Cover ';
					}
				}
				/************* 12.Calculation of Bearish Dark Cloud Cover Pattern.***********************/
			}
		}
	}
	return $pattern;
}
