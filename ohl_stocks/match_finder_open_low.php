<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "daibik";
$table = "match_finder_open_low_stocks";

$symbol = trim($_GET['symbol']);
//$symbol = 'ACC';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error || empty( $symbol )) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	$csv = array_map( 'str_getcsv', file( 'MODIFIED/' . $symbol . '.csv' ) );

	/*print count($csv) . "<br />";
    print '<pre>';
    print_r($csv);*/

	for ( $i=0; $i<=(count($csv) - 5); $i++ ) {
		if ( $i > 0 ) {
			if ( trim( $csv[$i][3] ) == trim( $csv[$i][5] )) {
				// Condition 1
				$gap_of_days = -1;
				if ( (count($csv) - ($i + 1)) >= 4 ) {
					for ( $j=$i; $j<=(count($csv) - 5); $j++) {
						$gap_of_days++;
						if ( trim( $csv[$j+1][5] ) > trim( $csv[$j+2][5] ) ) {
							// Condition 2
							if ( trim( $csv[$j+2][4] ) < trim( $csv[$j+3][4] ) ) {
								// Condition 3
								if ( trim( $csv[$j+3][4] ) < trim( $csv[$j+4][4] ) ) {

									// Check whether the first three candles are red
									if ( ( $csv[$j+2][3] > $csv[$j+2][7]) &&
									     ( $csv[$j+3][3] > $csv[$j+3][7]) &&
									     ( $csv[$j+4][3] > $csv[$j+4][7])
									) {
										$first_three_red = 1;
									}
									else {
										$first_three_red = 0;
									}

									// Check whether fourth green
									if ( $csv[$j+1][3] <  $csv[$j+1][7]) {
										$fourth_green = 1;
									}
									else {
										$fourth_green = 0;
									}

									// Calculate the gap up opening percentage
									/*if ( $csv[$i][3] >= $csv[$i+1][7] ) {*/
									    $gap_up_opening = number_format(($csv[$i][3] - $csv[$i+1][7]), 2, '.', '');
										$gap_up_opening_percentage = number_format((($gap_up_opening/$csv[$i+1][7])*100), 2, '.', '');
									/*}
									else {
										$gap_up_opening_percentage = number_format(($csv[$i+1][7] - $csv[$i][3]), 2, '.', '');
									}*/

									// Calculate the difference between high price and closing price of previous day
									//if ( $csv[$i-1][4] >= $csv[$i-1][7] ) {
										$diff_high_close_prev_day = number_format(($csv[$i+1][4] - $csv[$i+1][7]), 2, '.', '');
										$diff_high_close_prev_day_percentage = number_format((($diff_high_close_prev_day/$csv[$i+1][4])*100), 2, '.', '');
									//}

									$sql = "INSERT INTO " . $table ." (symbol,
							                                      day_open_low, 
							                                      fourth_day,
							                                      gap_of_days, 
							                                      first_three_red, 
							                                      fourth_green, 
							                                      gap_up_opening, 
							                                      gap_up_opening_percentage, 
							                                      diff_high_close_prev_day, 
							                                      diff_high_close_prev_day_percentage, 
							                                      executed)
									       VALUES('" . $symbol . "',
									              '" . date( 'Y-m-d', strtotime( trim( $csv[$i][0] ) ) ). "',
									              '" . date( 'Y-m-d', strtotime( trim( $csv[$j+1][0] ) ) ) . "',
									              '" . $gap_of_days . "',
									              '" . $first_three_red . "',
									              '" . $fourth_green . "',
									              '" . $gap_up_opening . "',
									              '" . $gap_up_opening_percentage . "',
									              '" . $diff_high_close_prev_day . "',
									              '" . $diff_high_close_prev_day_percentage . "',
									              NOW()
		                                )";

									$conn->query($sql);

									/*print 'open low = ' .$csv[$i][0] . "<br />";
									print '4th Day = ' .$csv[$j+1][0] . "<br />";
									print 'Gap = ' .$gap_of_days . "<br />";
									print 'First Three Red = ' .$first_three_red . "<br />";
									print 'Fourth Green = ' .$fourth_green . "<br />";
									print 'Gap up opening = ' .$gap_up_opening . "<br />";
									print 'Gap up opening percentage = ' .$gap_up_opening_percentage . "<br />";
									print 'difference between high price and closing price of previous day = ' .$diff_high_close_prev_day . "<br />";
									print 'difference between high price and closing price of previous day percentage = ' .$diff_high_close_prev_day_percentage . "<br />";
									print "<br /><br />";*/
									print $symbol . " updated\r\n";
									break;
								}
							}
						}
					}
				}
			}
		}
	}
}

?>
