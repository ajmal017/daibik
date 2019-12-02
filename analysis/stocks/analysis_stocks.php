<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "surya";
$table = "analysis_stocks";

$symbol = trim($_GET['symbol']);
$symbol = str_replace('_', '&', $symbol);
//$symbol = 'MFSL';

print $symbol . "\r\n";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error || empty( $symbol )) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	$csv_raw = array_map( 'str_getcsv', file( 'MODIFIED/' . $symbol . '.csv' ) );

	/*print '<pre>';
	print_r($csv_raw);
	exit;*/
	$csv = array();

	foreach ( $csv_raw as $key => $value ) {
		if ( !empty( $value[0] ) ) {
			$csv[$key][0] = $value[0];
			$csv[$key][1] = $value[3];
			$csv[$key][2] = $value[4];
			$csv[$key][3] = $value[5];
			$csv[$key][4] = $value[7];
			$csv[$key][5] = $value[8];
		}
	}

	if ( !empty( $csv ) ) {
		foreach ( $csv as $key => $value ) {
			if ( $key > 0 ) {
				$date = date('Y-m-d', strtotime(trim( $value[0])));
				$open_price = trim( $value[1]);
				$high_price = trim( $value[2]);
				$low_price = trim( $value[3]);
				$close_price = trim( $value[4]);
				$total_length_data = count($csv) - 1;
				$average_volume = $current_volume = $average_volume_twenty_days = 0;
				$greater_than_average_last_10_days_volume = $percentage_increase = $percentage_volume_twenty_days = 0;

				// Average 10 days Volume Calculation
				if ( ($key + 9) <= $total_length_data ) {
					for ( $i = $key; $i <= ( $key + 9 ); $i ++ ) {
						if ( $i == $key ) {
							$current_volume = trim( $csv[ $i ][5] );
						}
						$average_volume = $average_volume + trim( $csv[ $i ][5] );
					}

					$average_volume = number_format( ( $average_volume / 10 ), 2, '.', '' );

					if ( $current_volume >= $average_volume ) {
						$greater_than_average_last_10_days_volume = 1;

						// Percentage Increase In Current Volume Compared To Average Volume.
						$percentage_increase = number_format( ( ( ( $current_volume - $average_volume ) / $average_volume ) * 100 ), 2, '.', '' );
					}
				}
				// Average 20 days Volume Calculation
				if ( ($key + 19) <= $total_length_data ) {
					for($i = $key; $i<=($key + 19); $i++) {
						if ( $i == $key ) {
							$current_volume = trim( $csv[$i][5]);
						}
						$average_volume_twenty_days = $average_volume_twenty_days + trim( $csv[$i][5]);
					}

				$average_volume_twenty_days = number_format(($average_volume_twenty_days/20), 2, '.', '');
				if ( $current_volume >= $average_volume_twenty_days ) {
					// Percentage Increase In Current Volume Compared To Average Volume.
					$percentage_volume_twenty_days = number_format(((($current_volume - $average_volume_twenty_days)/$average_volume_twenty_days)*100), 2, '.', '');
				}

				 //print $current_volume . "<br />";
				 //print $average_volume . "<br />";
				}

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
					//Check whether the previous three candles are bearish.
					/*if ( (trim($csv[$key+1][1]) >= trim($csv[$key+1][4])) &&
						 (trim($csv[$key+2][1]) >= trim($csv[$key+2][4])) &&
						 (trim($csv[$key+3][1]) >= trim($csv[$key+3][4]))
					) {
						// Check for lower high of previous three candles.
						if ( (trim($csv[$key+1][2]) <= trim($csv[$key+2][2])) &&
							 (trim($csv[$key+2][2]) <= trim($csv[$key+3][2]))
						) {
							// Check for lower low of previous three candles
							if ( (trim($csv[$key+1][3]) <= trim($csv[$key+2][3])) &&
								 (trim($csv[$key+2][3]) <= trim($csv[$key+3][3]))
							) {
								//print $symbol . '===========' . $date . '=============' . $percentage_length . "\r\n";
								insert($conn, $table, $symbol, $date, 'bullish', 'Bullish Pin Bar' );
							}
						}
					}*/
					insert($conn, $table, $symbol, $date, 'bullish', 'Paper Umbrella', $greater_than_average_last_10_days_volume, $percentage_increase, $percentage_volume_twenty_days, $average_volume );
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
					//Check whether the previous three candles are bullish.
					/*if ( (trim($csv[$key+1][1]) < trim($csv[$key+1][4])) &&
						 (trim($csv[$key+2][1]) < trim($csv[$key+2][4])) &&
						 (trim($csv[$key+3][1]) < trim($csv[$key+3][4]))
					) {
						// Check for higher high of previous three candles.
						if ( (trim($csv[$key+1][2]) > trim($csv[$key+2][2])) &&
							 (trim($csv[$key+2][2]) > trim($csv[$key+3][2]))
						) {
							// Check for higher low of previous three candles
							if ( (trim($csv[$key+1][3]) > trim($csv[$key+2][3])) &&
								 (trim($csv[$key+2][3]) > trim($csv[$key+3][3]))
							) {
								//print $symbol . '===========' . $date . '=============' . $percentage_length . "\r\n";
								insert($conn, $table, $symbol, $date, 'bearish', 'Bearish Pin Bar' );
							}
						}
					}*/
					insert($conn, $table, $symbol, $date, 'bearish', 'Shooting Star', $greater_than_average_last_10_days_volume, $percentage_increase, $percentage_volume_twenty_days, $average_volume );
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
					// Check for lower high of previous three candles.
					/*if ( (trim($csv[$key+1][2]) <= trim($csv[$key+2][2])) &&
					     (trim($csv[$key+2][2]) <= trim($csv[$key+3][2]))
					) {
						// Check for lower low of previous three candles
						if ( ( trim( $csv[ $key + 1 ][3] ) <= trim( $csv[ $key + 2 ][3] ) ) &&
						     ( trim( $csv[ $key + 2 ][3] ) <= trim( $csv[ $key + 3 ][3] ) )
						) {*/
							//print $symbol . '===========' . $date . '=============' . "\r\n";
							insert($conn, $table, $symbol, $date, 'bullish', 'Bullish One White Soldier', $greater_than_average_last_10_days_volume, $percentage_increase, $percentage_volume_twenty_days, $average_volume );
						//}
					//}
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
					// Check for higher high of previous three candles.
					/*if ( (trim($csv[$key+1][2]) >= trim($csv[$key+2][2])) &&
					     (trim($csv[$key+2][2]) >= trim($csv[$key+3][2]))
					) {
						// Check for lower high of previous three candles
						if ( ( trim( $csv[ $key + 1 ][3] ) >= trim( $csv[ $key + 2 ][3] ) ) &&
						     ( trim( $csv[ $key + 2 ][3] ) >= trim( $csv[ $key + 3 ][3] ) )
						) {*/
							//print $symbol . '===========' . $date . '=============' . "\r\n";
							insert($conn, $table, $symbol, $date, 'bearish', 'Bearish One Black Crow', $greater_than_average_last_10_days_volume, $percentage_increase, $percentage_volume_twenty_days, $average_volume );
						//}
					//}
				}

				/************* 4.Calculation of Bearish One Black Crow.***********************/

				/************* 5.Calculation of Bullish Engulfing Pattern.***********************/
				if ( $open_price < $close_price &&
				     ($key + 1) <= $total_length_data &&
				     trim($csv[$key+1][1]) >= trim($csv[$key+1][4]) &&
				     $close_price > trim($csv[$key+1][1]) &&
				     $open_price < trim($csv[$key+1][4])
				) {
					//print $symbol . '===========' . $date . '=============' . "\r\n";
					insert($conn, $table, $symbol, $date, 'bullish', 'Bullish Engulfing Pattern', $greater_than_average_last_10_days_volume, $percentage_increase, $percentage_volume_twenty_days, $average_volume );
				}
				/*************  5.Calculation of Bullish Engulfing Pattern.***********************/

				/************* 6.Calculation of Bearish Engulfing Pattern.***********************/
				if ( $open_price > $close_price &&
				     ($key + 1) <= $total_length_data &&
				     trim($csv[$key+1][1]) <= trim($csv[$key+1][4]) &&
				     $close_price < trim($csv[$key+1][1]) &&
				     $open_price > trim($csv[$key+1][4])
				) {
					//print $symbol . '===========' . $date . '=============' . "\r\n";
					insert($conn, $table, $symbol, $date, 'bearish', 'Bearish Engulfing Pattern', $greater_than_average_last_10_days_volume, $percentage_increase, $percentage_volume_twenty_days, $average_volume );
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
								//print $symbol . '===========' . $date . '=============' . "\r\n";
								insert($conn, $table, $symbol, $date, 'bullish', 'Bullish Morning Star Pattern', $greater_than_average_last_10_days_volume, $percentage_increase, $percentage_volume_twenty_days, $average_volume );
							}
						}
						else {
							if ( $open_price > trim($csv[$key+1][1]) &&
							     trim($csv[$key+2][4]) > trim($csv[$key+1][1])
							) {
								//print $symbol . '===========' . $date . '=============' . "\r\n";
								insert($conn, $table, $symbol, $date, 'bullish', 'Bullish Morning Star Pattern', $greater_than_average_last_10_days_volume, $percentage_increase, $percentage_volume_twenty_days, $average_volume );
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
								//print $symbol . '===========' . $date . '=============' . "\r\n";
								insert($conn, $table, $symbol, $date, 'bearish', 'Bearish Evening Star Pattern', $greater_than_average_last_10_days_volume, $percentage_increase, $percentage_volume_twenty_days, $average_volume );
							}
						}
						else {
							if ( $open_price < trim($csv[$key+1][4]) &&
							     trim($csv[$key+2][4]) < trim($csv[$key+1][4])
							) {
								//print $symbol . '===========' . $date . '=============' . "\r\n";
								insert($conn, $table, $symbol, $date, 'bearish', 'Bearish Evening Star Pattern', $greater_than_average_last_10_days_volume, $percentage_increase, $percentage_volume_twenty_days, $average_volume );
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
					insert($conn, $table, $symbol, $date, 'bullish', 'Bullish Harami Pattern', $greater_than_average_last_10_days_volume, $percentage_increase, $percentage_volume_twenty_days, $average_volume );
				}
				/************* 9.Calculation of Bullish Harami Pattern.***********************/

				/************* 10.Calculation of Bearish Harami Pattern.***********************/
				if ( ($key + 1) <= $total_length_data &&
				     $open_price > $close_price &&
				     trim($csv[$key+1][1]) < trim($csv[$key+1][4]) &&
				     $open_price < trim($csv[$key+1][4]) &&
				     $close_price > trim($csv[$key+1][1])
				) {
					insert($conn, $table, $symbol, $date, 'bearish', 'Bearish Harami Pattern', $greater_than_average_last_10_days_volume, $percentage_increase, $percentage_volume_twenty_days, $average_volume );
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

						insert($conn, $table, $symbol, $date, 'bullish', 'Piercing Pattern', $greater_than_average_last_10_days_volume, $percentage_increase, $percentage_volume_twenty_days, $average_volume );
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

						insert($conn, $table, $symbol, $date, 'bearish', 'Dark Cloud Cover', $greater_than_average_last_10_days_volume, $percentage_increase, $percentage_volume_twenty_days, $average_volume );
					}
				}
				/************* 12.Calculation of Bearish Dark Cloud Cover Pattern.***********************/
			}
		}
	}
}

function insert($conn, $table, $symbol, $date_matched, $type, $pattern_name, $greater_than_average_last_10_days_volume, $percentage_increase, $percentage_volume_twenty_days, $last_ten_days_average_volume ) {
	$sql = "INSERT INTO " . $table ." (symbol, date_matched, type, pattern_name, greater_than_average_last_10_days_volume, percentage_increase, percentage_volume_twenty_days, last_ten_days_average_volume, executed)
		       VALUES('" . $symbol . "',
		              '" . $date_matched . "',
		              '" . $type . "',
		              '" . $pattern_name . "',
		              '" . $greater_than_average_last_10_days_volume . "',
		              '" . $percentage_increase . "',
		              '" . $percentage_volume_twenty_days . "',
		              '" . $last_ten_days_average_volume . "',
		              NOW()
		              )";

	$conn->query($sql);
}
