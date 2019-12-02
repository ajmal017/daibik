<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


$symbol = trim($_GET['symbol']);
$symbol = str_replace('_', '&', $symbol);
//$symbol = 'TCS';

//print $symbol . "\r\n";

if (  empty( $symbol )) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	$csv_raw = array_map( 'str_getcsv', file( 'MODIFIED/' . $symbol . '.csv' ) );

	$csv = $info = $data = array();

	foreach ( $csv_raw as $key => $value ) {
		if ( !empty( $value[0] ) && $key > 0 ) {
			$csv[$key][0] = $value[0];
			$csv[$key][1] = trim( $value[3] ); //open
			$csv[$key][2] = trim( $value[4] ); //high
			$csv[$key][3] = trim( $value[5] ); //low
			$csv[$key][4] = trim( $value[7] ); //close
			$csv[$key][5] = trim( $value[8] ); //ltp
		}
	}
    //print '<pre>';
	//print_r( $csv );

	foreach ( $csv as $key => $value ) {
		if ( isset( $csv[$key] ) && $key == 1 ) {
		  if ( $csv[$key][1] > $csv[$key+1][4] ) {
			  $diff_prev = number_format((( $csv[$key][1] - $csv[$key+1][4] )/$csv[$key+1][4])*100, 2, '.', '');
			  if ( $diff_prev >= 2 ) {
				  print $csv[$key][0] . "  " . $symbol . "  Gap Up = " . $diff_prev . "\r\n";
			  }
		  }
		  elseif ( $csv[$key][1] < $csv[$key+1][4] ) {
			  $diff_prev = number_format((( $csv[$key+1][1] - $csv[$key][4] )/$csv[$key+1][4])*100, 2, '.', '');
			  if ( $diff_prev >= 2 ) {
				  print $csv[$key][0] . "  " . $symbol . "  Gap Down = " . $diff_prev . "\r\n";
			  }
		  }
		}
	}
}
