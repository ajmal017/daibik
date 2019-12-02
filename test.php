<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


$url = 'http://localhost/daibik/FO_CSV/HDFC.csv';

print $url;

$csv = array_map('str_getcsv', file($url));

print '<pre>';
print_r($csv);
?>

