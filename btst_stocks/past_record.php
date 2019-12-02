<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "tojo";
$table = "btst_stocks_past_records";

$symbol = trim($_GET['symbol']);
$symbol = str_replace('_', '&', $symbol);
//$symbol = 'TCS';

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
		if ( !empty( $value[0] ) ) {
			$csv[$key][0] = $value[0];
			$csv[$key][1] = $value[3];
			$csv[$key][2] = $value[4];
			$csv[$key][3] = $value[5];
			$csv[$key][4] = $value[7];
			$csv[$key][5] = $value[8];
		}
	}

	patternMatch($csv, $symbol, $conn);
}

function patternMatch($csv, $symbol, $conn) {
	if ( !empty( $csv ) ) {
		foreach ( $csv as $key => $value ) {
			if ( $key > 0 ) {
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

				if ( !empty( $pattern ) ) {
					$sql = "INSERT INTO `btst_stocks_past_records` (symbol, current_candlestick_pattern, executed) VALUES('".
					       $symbol . "', '" .
					       $pattern . "', '" .
                           date('Y-m-d', strtotime( $value[0] ) ) ."')";

					//print $sql . "<br />";
					$conn->query($sql);
				}
			}
		}
	}
	//return $pattern;
}
