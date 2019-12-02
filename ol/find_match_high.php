<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "daibik";
$table = "open_high";

$symbol = trim($_GET['symbol']);
//$symbol = 'AJANTPHARM';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error || empty( $symbol )) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	$csv = array_map( 'str_getcsv', file( 'MODIFIED/' . $symbol . '.csv' ) );
	/*print count($csv) . "<br />";
    print '<pre>';
    print_r($csv);
	exit;*/


	for ( $i=0; $i<=(count($csv) - 5); $i++ ) {
		if ( $i > 0 ) {
			if ( trim( $csv[$i][1] ) == trim( $csv[$i][2] )) {
				// Condition 1
				$gap_of_days = 0;
				if ( (count($csv) - ($i + 1)) >= 4 ) {
					for ( $j=$i; $j<=(count($csv) - 5); $j++) {
						$gap_of_days++;
						if ( trim( $csv[$j+1][2] ) < trim( $csv[$j+2][2] ) ) {
							// Condition 2
							if ( trim( $csv[$j+2][3] ) > trim( $csv[$j+3][3] ) ) {
								// Condition 3
								if ( trim( $csv[$j+3][3] ) > trim( $csv[$j+4][3] ) ) {
									$sql = "INSERT INTO " . $table ." (symbol, 
							                                      day_open_high, 
							                                      fourth_day, 
							                                      third_day, 
							                                      second_day, 
							                                      first_day, 
							                                      gap_of_days, 
							                                      executed)
									       VALUES('" . $symbol . "',
									              '" . date( 'Y-m-d', strtotime( trim( $csv[$i][0] ) ) ). "',
									              '" . date( 'Y-m-d', strtotime( trim( $csv[$j+1][0] ) ) ) . "',
									              '" . date( 'Y-m-d', strtotime( trim( $csv[$j+2][0] ) ) ) . "',
									              '" . date( 'Y-m-d', strtotime( trim( $csv[$j+3][0] ) ) ) . "',
									              '" . date( 'Y-m-d', strtotime( trim( $csv[$j+4][0] ) ) ) . "',
									              '" . $gap_of_days . "',
									              NOW()
		                                )";

									$conn->query($sql);

									/*print 'open low = ' .$csv[$i][0] . "<br />";
									print '4th Day = ' .$csv[$j+1][0] . "<br />";
									print '3rd Day = ' .$csv[$j+2][0] . "<br />";
									print '2nd Day = ' .$csv[$j+3][0] . "<br />";
									print '1st Day = ' .$csv[$j+4][0] . "<br />";
									print 'Gap = ' .$gap_of_days . "<br />";
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
