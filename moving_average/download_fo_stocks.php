<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$file = 'fo_stocks.csv';
//print $file . '<br />';
$csv = array_map('str_getcsv', file($file));

$stocks = array();
foreach ( $csv as $key => $value ) {
   if ( $key > 0 ) {
	   $stocks[]['symbol'] = $value[0];
   }
}

$data_json = json_encode($stocks);

file_put_contents('fo_stocks.json', $data_json);

?>
