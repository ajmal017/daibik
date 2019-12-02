<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$symbol = trim($_GET['symbol']);
$symbol = str_replace('_', '&', $symbol);
//$symbol = 'DLF';

//print $symbol . "\r\n";

if ( empty( $symbol )) {
	die( $symbol . " not found " );
}
else {
	$csv_raw = array_map( 'str_getcsv', file( 'MODIFIED/' . $symbol . '.csv' ) );

	//print '<pre>';
	//print_r($csv_raw);

	$first_month = [];
	$second_month = [];
	$third_month = [];
	foreach ( $csv_raw as $key => $value ) {
       if ( $key > 0 ) {
	       if ( $key >= 1 && $key <= 21 ) {
		       $first_month[] = $value;
	       }

	       if ( $key >= 22 && $key <= 41 ) {
		       $second_month[] = $value;
	       }

	       if ( $key >= 42 ) {
		       $third_month[] = $value;
	       }
       }
	}

	$fp = fopen('MODIFIED_NEW/' . $symbol . '_first.csv', 'w');
	foreach ($first_month as $fields) {
		fputcsv($fp, $fields);
	}
	fclose($fp);

	$fp = fopen('MODIFIED_NEW/' . $symbol . '_second.csv', 'w');
	foreach ($second_month as $fields) {
		fputcsv($fp, $fields);
	}
	fclose($fp);

	$fp = fopen('MODIFIED_NEW/' . $symbol . '_third.csv', 'w');
	foreach ($third_month as $fields) {
		fputcsv($fp, $fields);
	}
	fclose($fp);

	/*print '<pre>';
	print_r($second_month);
	print_r($third_month);*/

}
?>
