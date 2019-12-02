<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "surya";
$table = "share_health";

$symbol = trim($_GET['symbol']);
$symbol = str_replace('_', '&', $symbol);
//$symbol = 'TCS';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error || empty( $symbol )) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	$csv_raw = array_map( 'str_getcsv', file( 'MODIFIED/' . $symbol . '.csv' ) );

	$csv = array();

	foreach ( $csv_raw as $key => $value ) {
		$csv[$key][0] = $value[0];
		$csv[$key][1] = $value[3];
		$csv[$key][2] = $value[4];
		$csv[$key][3] = $value[5];
		$csv[$key][4] = $value[7];
		$csv[$key][5] = $value[8];
	}

	//print '<pre>';
	//print_r($csv);

    if ( !empty( $csv ) ) {
	    foreach ( $csv as $key => $value ) {
		    if ( $key > 0 && $key <=1) {
			    $date = date('Y-m-d', strtotime(trim( $value[0])));
			    $open_price = trim( $value[1]);
			    $high_price = trim( $value[2]);
			    $low_price = trim( $value[3]);
			    $close_price = trim( $value[4]);
			    $total_length_data = count($csv) - 1;
			    $average_volume = $current_volume = 0;
			    $greater_than_average_last_10_days_volume = $percentage_increase = 0;
			    $candlestick_pattern = '';

			    // Average Volume Calculation
			    if ( ($key + 9) <= $total_length_data ) {
				    for($i = $key; $i<=($key + 9); $i++) {
					    if ( $i == $key ) {
						    $current_volume = trim( $csv[$i][5]);
					    }
					    $average_volume = $average_volume + trim( $csv[$i][5]);
				    }

				    $average_volume = number_format(($average_volume/10), 2, '.', '');

				    //print $current_volume . "<br />";
				    //print $average_volume . "<br />";

				    if ( $current_volume >= $average_volume ) {
					    $greater_than_average_last_10_days_volume = 1;

					    // Percentage Increase In Current Volume Compared To Average Volume.
					    $percentage_increase = number_format(((($current_volume - $average_volume)/$average_volume)*100), 2, '.', '');
				    }
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
				    $candlestick_pattern .= 'Paper Umbrella ';
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
				    $candlestick_pattern .= 'Shooting Star ';
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
				    $candlestick_pattern .= 'Bullish One White Soldier ';
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
				    $candlestick_pattern .= 'Bearish One Black Crow ';
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
				    $candlestick_pattern .= 'Bullish Engulfing Pattern ';
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
				    $candlestick_pattern .= 'Bearish Engulfing Pattern ';
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
							    $candlestick_pattern .= 'Bullish Morning Star Pattern ';
						    }
					    }
					    else {
						    if ( $open_price > trim($csv[$key+1][1]) &&
						         trim($csv[$key+2][4]) > trim($csv[$key+1][1])
						    ) {
							    //print $symbol . '===========' . $date . '=============' . "\r\n";
							    $candlestick_pattern .= 'Bullish Morning Star Pattern ';
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
							    $candlestick_pattern .= 'Bearish Evening Star Pattern  ';
						    }
					    }
					    else {
						    if ( $open_price < trim($csv[$key+1][4]) &&
						         trim($csv[$key+2][4]) < trim($csv[$key+1][4])
						    ) {
							    //print $symbol . '===========' . $date . '=============' . "\r\n";
							    $candlestick_pattern .= 'Bearish Evening Star Pattern  ';
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
				    $candlestick_pattern .= 'Bullish Harami Pattern ';
			    }
			    /************* 9.Calculation of Bullish Harami Pattern.***********************/

			    /************* 10.Calculation of Bearish Harami Pattern.***********************/
			    if ( ($key + 1) <= $total_length_data &&
			         $open_price > $close_price &&
			         trim($csv[$key+1][1]) < trim($csv[$key+1][4]) &&
			         $open_price < trim($csv[$key+1][4]) &&
			         $close_price > trim($csv[$key+1][1])
			    ) {
				    $candlestick_pattern .= 'Bearish Harami Pattern ';
			    }
			    /************* 10.Calculation of Bearish Harami Pattern.***********************/

			    /************* 11.Calculation of Bullish Piercing Pattern.***********************/
			    if ( ($key + 1) <= $total_length_data &&
			         $open_price < $close_price &&
			         trim($csv[$key+1][1]) > trim($csv[$key+1][4])
			    ) {
				    $range_current = $close_price - $open_price;
				    $range_previous = $open_price - $close_price;
				    $range_ratio = number_format((($range_current/$range_previous)*100), 2, '.', '');
				    if ( $range_ratio >= 50 && $range_ratio <= 100 ) {

					    $candlestick_pattern .= 'Piercing Pattern ';
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

					    $candlestick_pattern .= 'Dark Cloud Cover  ';
				    }
			    }
			    /************* 12.Calculation of Bearish Dark Cloud Cover Pattern.***********************/

			    insert($conn, $table, $symbol, $average_volume, $candlestick_pattern, $greater_than_average_last_10_days_volume, $percentage_increase );
			    //update($conn, $table, $symbol, $average_volume, $candlestick_pattern, $greater_than_average_last_10_days_volume, $percentage_increase );
		    }
	      }
	    }
}

function insert($conn, $table, $symbol, $last_ten_days_average_volume, $candlestick_pattern, $current_volume_greater_than_average_last_ten_days_volume, $current_volume_greater_than_average_last_ten_days_volume_percent ) {
	$sql = "INSERT INTO " . $table ." (symbol, 
	                                   last_ten_days_average_volume, 
	                                   candlestick_pattern, 
	                                   current_volume_greater_than_average_last_ten_days_volume,
	                                   current_volume_greater_than_average_last_ten_days_volume_percent,
	                                   executed
	                                   )
		       VALUES('" . $symbol . "',
		              '" . $last_ten_days_average_volume . "',
		              '" . $candlestick_pattern . "',
		              '" . $current_volume_greater_than_average_last_ten_days_volume . "',
		              '" . $current_volume_greater_than_average_last_ten_days_volume_percent . "',
		               NOW()
		              )";

	$conn->query($sql);
}

function update($conn, $table, $symbol, $last_ten_days_average_volume, $candlestick_pattern, $current_volume_greater_than_average_last_ten_days_volume, $current_volume_greater_than_average_last_ten_days_volume_percent ) {
	$sql = "UPDATE ". $table . " SET last_ten_days_average_volume=" . $last_ten_days_average_volume ." ," .
	                                 "candlestick_pattern='" . $candlestick_pattern ."' ," .
	                                 "current_volume_greater_than_average_last_ten_days_volume=" . $current_volume_greater_than_average_last_ten_days_volume ." ," .
	                                 "current_volume_greater_than_average_last_ten_days_volume_percent=" . $current_volume_greater_than_average_last_ten_days_volume_percent .
	                                 "executed=" .  NOW() .
	        " WHERE symbol='" . $symbol . "'";
	$conn->query($sql);
}
?>
