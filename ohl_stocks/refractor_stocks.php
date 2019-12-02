<?php
$filename = trim($_GET['symbol']);

$filename = str_replace('_', '&', $filename);
//$filename = str_replace('%26', '', $filename);

print $filename. "\r\n";

// Creating a new csv removing unabundant spaces and data
$csv = array_map('str_getcsv', file('DUMP/'. $filename . '.csv'));

$fp = fopen('MODIFIED/' . $filename . '.csv', 'w');
foreach ( $csv as $key => $value ) {
	//if ( $key > 21 && $key < 46 && !empty($value[0]) ) { // for 1 month
	if ( $key > 21 && $key < 88 && !empty($value[0]) ) { // for 3 months
		fputcsv($fp, $value);
	}
}
fclose($fp);

?>
