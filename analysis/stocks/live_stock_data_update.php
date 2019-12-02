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
//$symbol = 'TCS';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error || empty( $symbol )) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	//$url = 'http://localhost/daibik/analysis/stocks/downloads/'. $symbol .'.html';
	//$html = file_get_contents($url);

	$json = file_get_contents('downloads/' . $symbol . '.json');
	
	$csv_raw = array_map( 'str_getcsv', file( 'MODIFIED/' . $symbol . '.csv' ) );

	/*print '<pre>';
	print_r( $csv_raw );
	exit;*/

	$csv = $info = array();

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


	$stock_page = new DOMDocument();

	libxml_use_internal_errors(TRUE); //disable libxml errors

	//if(!empty($html)) {
	if(!empty($json)) {
		/*$stock_page->loadHTML($html);

		libxml_clear_errors(); //

		$stock_page_xpath = new DOMXPath($stock_page);

		$data = $stock_page_xpath->query('//div[@id="responseDiv"]');*/

		$data = json_decode( $json );

		/*print '<pre>';
		print_r($data);
		exit;*/

        /*if($data->length > 0 ) {
	        foreach ( $data as $row ) {
		        $response = json_decode($row->nodeValue);

		        $date = date('d-M-Y');
		        $open_price = $response->data[0]->open;
		        $high_price = $response->data[0]->dayHigh;
		        $low_price = $response->data[0]->dayLow;
		        $close_price = $response->data[0]->lastPrice;
		        $volume = str_replace(',', '', $response->data[0]->totalTradedVolume);*/

		 if( !empty( $data->data[0] ) ) {

		    $response = $data;

		    $date = date('d-M-Y');
		    $open_price = $response->data[0]->open;
		    $high_price = $response->data[0]->dayHigh;
		    $low_price = $response->data[0]->dayLow;
			$close_price = $response->data[0]->lastPrice;
		    $volume = str_replace(',', '', $response->data[0]->totalTradedVolume);

			$percentile_change_in_price =  trim( $response->data[0]->pChange );

            $output_1 = array_slice($csv, 0, 1);
	        $output_latest = [
		        1 => [
			        0 => $date,
			        1 => str_replace( ',', '', $open_price ),
			        2 => str_replace( ',', '', $high_price ),
			        3 => str_replace( ',', '', $low_price ),
			        4 => str_replace( ',', '', $close_price ),
			        5 => $volume,
		        ]
	        ];
            $output_2 = array_slice($csv, 1);

            $output = array_merge($output_1,$output_latest, $output_2);

		        //print '<pre>';
		        //print_r($output);


	        $info['current_candlestick_pattern'] = patterMatch($output);
	        //print 'Latest Pattern = ' . $info['current_candlestick_pattern'] . '<br />';

	        $info['no_of_dry_day'] = dryDayCount($symbol, $conn);
			//print 'Dry Day Count = ' . $info['no_of_dry_day']. '<br />';

			$latest_percentage_calculation = latestPercentageCalculation($output);
			//print 'Percentage Increase = ' . $info['percentage_increase_volume'] . '<br />';

			$previous_pattern_match = previousPatternMatch( $symbol, $conn );

	        $info = array_merge($info, $previous_pattern_match, $latest_percentage_calculation);

	        $info['symbol'] = $symbol;

	        $info['current_price'] = $percentile_change_in_price;

	        //print '<pre>';
	        //print_r($info);

	        updateData($info, $conn);

			insertIntoDetails($info, $conn);

	        mysqli_close($conn);

        }
	}
}

function insertIntoDetails( $data, $conn ) {
  $sql = "INSERT INTO `live_feed_details_stock` (symbol, current_price_percentile_change, total_percentage_volume, executed) VALUES('".
           $data['symbol'] . "', " .
           $data['current_price'] . ", " .
           $data['total_percentage_volume'] . ", 
           NOW())";

  $conn->query($sql);
}

function updateData($data, $conn) {
  $sql = "UPDATE live_feed_stocks SET
                           current_candlestick_pattern='" . $data['current_candlestick_pattern'] . "', 
                           percentage_increase_volume='". $data['percentage_increase_volume'] ."', 
                           total_percentage_volume='". $data['total_percentage_volume'] ."', 
                           no_of_dry_day='". $data['no_of_dry_day'] ."', 
                           previous_candlestick_pattern='". $data['previous_candlestick_pattern'] ."', 
                           previous_candlestick_pattern_date='". $data['previous_candlestick_pattern_date'] ."', 
                           current_volume='". $data['current_volume'] ."', 
                           average_volume='". $data['average_volume'] ."', 
                           current_price='". $data['current_price'] ."', 
                           executed=NOW()
          WHERE symbol='". $data['symbol'] ."'                 
         ";

	$conn->query($sql);
	print $data['symbol']. "\r\n";

	/*$info = file_get_contents('live_feed.json');
	$info = json_decode($info);

	foreach ( $info as $key => $value ) {
       if ( $value->symbol == $data['symbol'] ) {
	       $info[$key]->current_candlestick_pattern = $data['current_candlestick_pattern'];
	       $info[$key]->percentage_increase_volume = $data['percentage_increase_volume'];
	       $info[$key]->no_of_dry_day = $data['no_of_dry_day'];
	       $info[$key]->previous_candlestick_pattern = $data['previous_candlestick_pattern'];
	       $info[$key]->previous_candlestick_pattern_date = $data['previous_candlestick_pattern_date'];
	       $info[$key]->current_volume = $data['current_volume'];
	       $info[$key]->average_volume = $data['average_volume'];
	       $info[$key]->executed = date('d-M-Y H:i:s');
	       break;
       }
	}*/

	//file_put_contents('live_feed.json', $info);
}

function previousPatternMatch( $symbol, $conn ) {
   $data['previous_candlestick_pattern_date'] = '';
   $data['previous_type'] = '';
   $data['previous_candlestick_pattern'] = '';

   $sql = "SELECT * FROM analysis_stocks WHERE symbol ='". $symbol . "' LIMIT 1";
   $query=mysqli_query($conn,$sql);
    while ( $result = mysqli_fetch_array($query,MYSQLI_ASSOC) ) {
	    $data['previous_candlestick_pattern_date'] = $result['date_matched'];
	    $data['previous_type'] = $result['type'];
	    $data['previous_candlestick_pattern'] = $result['pattern_name'];
    }

	//print '<pre>';
	//print_r($data);

	return $data;
}

function latestPercentageCalculation($csv) {
	$current_volume = $average_volume = $percentage_increase = 0;
	$data['current_volume'] = 0;
	$data['average_volume'] = 0;
	$data['percentage_increase_volume'] = 0;

    if ( !empty( $csv ) ) {
	    for ( $key = 1; $key <= 10; $key++ ) {
		    if ( $key == 1 ) {
			    $current_volume = trim( $csv[ $key ][5] );
		    }
		    $average_volume = $average_volume + trim( $csv[ $key ][5] );
	    }
	    $average_volume = number_format( ( $average_volume / 10 ), 2, '.', '' );

	    if ( $current_volume >= $average_volume ) {
		    // Percentage Increase In Current Volume Compared To Average Volume.
		    $data['percentage_increase_volume'] = number_format( ( ( ( $current_volume - $average_volume ) / $average_volume ) * 100 ), 2, '.', '' );
	    }
	    $data['total_percentage_volume'] = number_format( ( ( $current_volume / $average_volume ) * 100 ), 2, '.', '' );
    }

	$data['current_volume'] = $current_volume;
	$data['average_volume'] = $average_volume;
	//print $average_volume  . '<br />';
	return $data;
}

function dryDayCount($symbol, $conn) {
	$symbol = trim($symbol);
	$no_dry_day = 0;

	$sql = "SELECT * FROM volume_analysis_stocks WHERE symbol ='". $symbol . "'";
	$query=mysqli_query($conn,$sql);
	while ( $result = mysqli_fetch_array($query,MYSQLI_ASSOC) ) {
		if ( $result['percentage_increase'] >= 0 &&  $result['percentage_increase'] <= 5) {
			$no_dry_day++;
		}
		else {
			break;
		}
	}

	return $no_dry_day;
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
?>
