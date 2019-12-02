<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "daibik";
$table = "next_match_open_high";

$symbol = trim($_GET['symbol']);
//$symbol = 'APOLLOHOSP';

//print $symbol . " started<br />";

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

	for ( $i=0; $i<=(count($csv) - 4); $i++ ) {
		if ( $i > 0 ) {
			// Condition 1
			if ( trim( $csv[$i][2] ) <= trim( $csv[$i+1][2] ) ) {
				// Condition 2
				if ( trim( $csv[$i+1][3] ) > trim( $csv[$i+2][3] ) ) {
					// Condition 3
					if ( trim( $csv[$i+2][3] ) > trim( $csv[$i+3][3] ) ) {
						/*print '4th Day = ' .$csv[$i][0] . "<br />";
						print '3rd Day = ' .$csv[$i+1][0] . "<br />";
						print '2nd Day = ' .$csv[$i+2][0] . "<br />";
						print '1st Day = ' .$csv[$i+3][0] . "<br />";
						print 'Gap = ' .$i . "<br />";*/

						$oh_count = 0;
						// Check how many times Open-High happened
						for ( $j=$i-1; $j>0; $j--) {
							if ( trim( $csv[$j][1] ) == trim( $csv[$j][2] )) {
								$oh_count++;
							}
						}
						//print 'OL appeared = ' .$oh_count . "<br />";

						$sql = "INSERT INTO " . $table ." (symbol,
					                                       matched, 
					                                       gap, 
					                                       total_oh_appeared,
					                                       executed)
									       VALUES('" . $symbol . "',
									              '" . date( 'Y-m-d', strtotime( trim( $csv[$i][0] ) ) ). "',
									              '" . ($i-1) . "',
									              '" . $oh_count . "',
									              NOW()
		                                )";

						$conn->query($sql);

						print $symbol . " updated\r\n";
						break;
					}
				}
			}
		}
	}
}


?>
