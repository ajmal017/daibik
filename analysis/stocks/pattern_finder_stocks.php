<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$symbol = trim($_GET['symbol']);
$symbol = str_replace('_', '&', $symbol);
//$symbol = 'DLF';

//print $symbol . "\r\n";
// Create connection

if ( empty( $symbol )) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	//$json = file_get_contents('downloads/' . $symbol . '.json');

	$csv_raw = array_map( 'str_getcsv', file( 'MODIFIED/' . $symbol . '.csv' ) );
	//$csv_raw = array_map( 'str_getcsv', file( 'MODIFIED_NEW/' . $symbol . '_third.csv' ) );
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
			$csv[$key][4] = $value[6];
			$csv[$key][5] = $value[8];
		}
	}

	//$data = json_decode( $json );

	//print '<pre>';
	//print_r( $csv );
	/*exit;*/
    //if ( !empty( $data ) ) {
	    /*$output_1 = array_slice($csv, 0, 1);
	    $output_latest = [
		    1 => [
			    0 => date('d-M-Y'),
			    1 => str_replace( ',', '', trim( $data->data[0]->open ) ),
			    2 => str_replace( ',', '', trim( $data->data[0]->dayHigh ) ),
			    3 => str_replace( ',', '', trim( $data->data[0]->dayLow ) ),
			    4 => str_replace( ',', '', trim( $data->data[0]->lastPrice ) ),
			    5 => str_replace( ',', '', trim( $data->data[0]->totalTradedVolume ) ),
		    ]
	    ];
	    $output_2 = array_slice($csv, 1);

	    $output = array_merge($output_1,$output_latest, $output_2);*/

	    /*print '<pre>';
		print_r( $output );*/
	    /*exit;*/

	$output = $csv;
	$total_length = count($output);
	/*******================================== OLD Code =======================================*********************/
	/*$low = 0;
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
			if ( abs( $percentage_increase ) < 3 && $next_day_high_open < 1 ) {
			//if ( $output[$low_key][0] == '24-Aug-2018') {
			print 'Buy Side ' . $symbol . '  ' . $output[$low_key][0] . '   '. $percentage_increase. '    ' . $low_close_percentage . '  ' . $next_day_high_open . "\r\n";
			//}
			}
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

			if ( abs( $percentage_decrease ) < 3 && abs( $next_day_open_low ) < 1 ) {
			//if ( $output[$high_key][0] == '24-Aug-2018') {
			print 'Sell Side ' . $symbol . '  ' . $output[$high_key][0] . '   '. $percentage_decrease. '    ' . $high_close_percentage . '   ' . $next_day_open_low . "\r\n";
			//}
			}
		}
	}*/
	/*******================================== OLD Code =======================================*********************/

	foreach ( $output as $key => $value ) {
		if ( $key > 0 ) {
			$cur_date            = trim( $value[0] );
			$date_one_month_back = date( 'd-M-Y', strtotime( "$cur_date -29 day" ) );
			//print $cur_date . '  ' . $date_one_month_back . "<br />";
			$low      = 0;
			$low_key  = $next_day_high_open = 0;
			$high     = 0;
			$high_key = $next_day_open_low = 0;

			$low     = trim( $output[$key][3] );
			$low_key = $key;

			$high     = trim( $output[$key][2] );
			$high_key = $key;

			$average_volume = $total_percentage_volume = 0;
			if ( ( $key + 10 ) <= ( $total_length - 1 ) ) {
				for ( $ave_vol = $key; $ave_vol <= $key + 10; $ave_vol++ ) {
					if ( $ave_vol == $key ) {
						$current_volume = trim( $output[ $ave_vol ][5] );
					}
					$average_volume = $average_volume + trim( $output[ $ave_vol ][5] );
				}
				$average_volume = number_format( ( $average_volume / 10 ), 2, '.', '' );
				$total_percentage_volume = number_format( ( ( ( $current_volume - $average_volume ) / $average_volume ) * 100 ), 2, '.', '' );
			}

			//print 'Outer Loop = ' . $key. '  ' . $low_key . "<br />";
			if ( ( $key + 28 ) <= ( $total_length - 1 ) ) {
				//print "Entered " . "<br />";
				for ( $i = $key+1; $i <= ( $key + 28 ); $i++ ) {
					if ( trim( $output[$i][3] ) < $low ) {
						$low_key = $i;
						//print 'Low = '. $low_key. "<br />";
						break;
					}
				}
				if ( $low_key == $key ) {
					//print 'Low Key = '. $low_key. "\r\n";
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
							if ( abs( $next_day_high_open ) > 0.5 ) {
								if ( $output[$low_key][0] !== '21-Sep-2018') {
								 print 'Buy Side ' . $symbol . '  ' . $output[$low_key][0] . '   '. $percentage_increase. '    ' . $low_close_percentage . '  ' . $next_day_high_open . '  ' . $total_percentage_volume . "\r\n";
								}
							}
						}
					}
			}
			}

			if ( ( $key + 28 ) <= ( $total_length - 1 ) ) {
				//print $key. "<br />";
				for ( $j = $key + 1; $j <= ( $key + 28 ); $j++ ) {
					if ( trim( $output[$j][2] ) > $high ) {
						$high_key = $j;
						break;
					}
				}
				if ( $high_key == $key ) {
					//print 'High = '. $high_key. "<br />";
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

							if ( abs( $next_day_open_low ) > 0.5 ) {
								if ( $output[$high_key][0] !== '21-Sep-2018') {
								 print 'Sell Side ' . $symbol . '  ' . $output[$high_key][0] . '   '. $percentage_decrease. '    ' . $high_close_percentage . '   ' . $next_day_open_low . '  ' . $total_percentage_volume . "\r\n";
								}
							}
						}
					}
				}
			}
		}
	}
}
