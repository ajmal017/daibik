<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "surya";
$table = "volume_analysis_stocks";

$symbol = trim($_GET['symbol']);
$symbol = str_replace('_', '&', $symbol);
//$symbol = 'EXIDEIND';

print $symbol . "\r\n";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error || empty( $symbol )) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	$csv_raw = array_map( 'str_getcsv', file( 'MODIFIED/' . $symbol . '.csv' ) );

	$csv = array();

	foreach ( $csv_raw as $key => $value ) {
		if ( !empty( $value[0] ) ) {
			$csv[ $key ][0] = $value[0];
			$csv[ $key ][1] = $value[3];
			$csv[ $key ][2] = $value[4];
			$csv[ $key ][3] = $value[5];
			$csv[ $key ][4] = $value[7];
			$csv[ $key ][5] = $value[8];
		}
	}

	/*print '<pre>';
	print_r($csv);*/
	//exit;

	if ( !empty( $csv ) ) {
		foreach ( $csv as $key => $value ) {
			if ( $key > 0 && $key <= 20) {
				$date = date('Y-m-d', strtotime(trim( $value[0])));
				$close_price = trim( $value[4]);
				$average_volume = $current_volume = $percentage_increase = $greater_than_average_last_10_days_volume = $percentage_increase = $percentage_previous_day = 0;
				$prev_average_volume = 0;
				$total_length_data = count($csv) - 1;

				/*if ( $key == 1 ) {
					pastTrendCalculation($conn, $csv, $symbol);
				}*/

				// Average 10 days Volume Calculation
				if ( ($key + 9) <= $total_length_data ) {
					for ( $i = $key; $i <= ( $key + 9 ); $i++ ) {
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

					if ( (($key + 1) + 9) <= $total_length_data ) {
						for ( $i = ($key + 1); $i <= ( ($key + 1) + 9 ); $i++ ) {
							if ( $i == ($key + 1) ) {
								$prev_current_volume = trim( $csv[ $i ][5] );
							}
							$prev_average_volume = $prev_average_volume + trim( $csv[ $i ][5] );
						}
						$prev_average_volume = number_format( ( $prev_average_volume / 10 ), 2, '.', '' );
						$prev_percentage_increase = number_format( ( ( ( $average_volume - $prev_average_volume ) / $prev_average_volume ) * 100 ), 2, '.', '' );
					}

					$total_percentage_volume = number_format( ( ( $current_volume / $average_volume ) * 100 ), 2, '.', '' );

                    //print $average_volume . '   ' . $prev_average_volume . '  ' .$prev_percentage_increase . "<br />";
					$sql = "INSERT INTO " . $table ." (symbol, executed, average_volume, closing_price, greater_than_average_last_10_days_volume, percentage_increase, total_percentage_volume, percentage_previous_day)
		       VALUES('" . $symbol . "',
		              '" . $date . "',
		              '" . $average_volume . "',
		              '" . $close_price . "',
		              '" . $greater_than_average_last_10_days_volume . "',
		              '" . $percentage_increase . "',
		              '" . $total_percentage_volume . "',
		              '" . $prev_percentage_increase . "'
		              )";

					$conn->query($sql);
				}
			}
		}
	}
}

function pastTrendCalculation($conn, $csv, $symbol) {
  $type = '';
  $continuation = 0;
  $length = count( $csv );
  foreach ( $csv as $key => $value ) {
     if ( $key > 0 && ($key+1) < $length ) {
        if ( ( $csv[$key][5] > $csv[$key+1][5] ) &&
             ( $csv[$key][4] <= $csv[$key+1][4] ) &&
             ( $type == '' || $type == 'Volume Increase Price Decrease' )
        ) {
	        $type = 'Volume Increase Price Decrease';
	        $continuation++;
        }
	    elseif ( ( $csv[$key][5] > $csv[$key+1][5] ) &&
	          ( $csv[$key][4] >= $csv[$key+1][4] ) &&
	          ( $type == '' || $type == 'Volume Increase Price Increase' )
	     ) {
		     $type = 'Volume Increase Price Increase';
		     $continuation++;
	    }
        else if ( ( $csv[$key][5] < $csv[$key+1][5] ) &&
	          ( $csv[$key][4] <= $csv[$key+1][4] ) &&
	          ( $type == '' || $type == 'Volume Decrease Price Decrease' )
	     ) {
		     $type = 'Volume Decrease Price Decrease';
		     $continuation++;
	     }
	     else if ( ( $csv[$key][5] < $csv[$key+1][5] ) &&
	          ( $csv[$key][4] >= $csv[$key+1][4] ) &&
	          ( $type == '' || $type == 'Volume Decrease Price Increase' )
	     ) {
		     $type = 'Volume Decrease Price Increase';
		     $continuation++;
	     }
	     else {
		     //print 'Key = ' . $key . "<br />";
		     break;
	     }
     }
  }

  //print 'type = ' . $type . "\r\n";

  $sql = "UPDATE `live_feed_stocks` 
          SET past_trend_name='" . $type . "', 
          past_trend_continuation=" . $continuation . " WHERE symbol='". $symbol ."'";

  $conn->query($sql);
}

function updateVolumePercentage( $conn, $symbol ){
  $select_sql = "SELECT total_percentage_volume FROM `volume_analysis_stocks` WHERE symbol='" . $symbol . "'";
  $query=mysqli_query($conn,$select_sql);
  while ( $result = mysqli_fetch_array($query,MYSQLI_ASSOC) ) {
		$data[] = $result;
  }
}
?>
