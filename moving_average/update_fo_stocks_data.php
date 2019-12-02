<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('FIVE_DAYS_PERIOD', 5);
define('FIFTEEN_DAYS_PERIOD', 15);
define('SMOOTHNING_CONSTANT_FIVE_DAYS_PERIOD', 2/(FIVE_DAYS_PERIOD + 1));
define('SMOOTHNING_CONSTANT_FIFTEEN_DAYS_PERIOD', 2/(FIFTEEN_DAYS_PERIOD + 1));

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "daibik";
$table = "moving_averages";

$symbol = trim($_GET['symbol']);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error || empty( $symbol )) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	$url = 'http://localhost/daibik/FO_CSV/'. $symbol .'.csv';
	$csv = array_map('str_getcsv', file($url));

	/*print '<pre>';
    print_r($csv);*/

	foreach ( $csv as $key => $value ) {
		if ( isset( $value[8] ) && !empty($value[0])  ) {
			$value[0] = trim($value[0]);
			$value[1] = trim($value[1]);
			$value[2] = trim($value[2]);
			$value[3] = trim($value[3]);
			$value[4] = trim($value[4]);
			$value[5] = trim($value[5]);
			$value[6] = trim($value[6]);
			$value[7] = trim($value[7]);
			$value[8] = trim($value[8]);
			$value[9] = trim($value[9]);
			$input[] = $value;
		}
	}

	$input = array_slice($input, 2);
	$input = array_reverse($input);

	$output = array();
	$counter = 0;
	foreach ( $input as $key => $value ) {
		$output[$counter]['date'] = $value[0];
		$output[$counter]['close'] = $value[7];
		$counter++;
	}
	for( $i = 0; $i <= (FIVE_DAYS_PERIOD - 2); $i++ ) {
		$output[$i]['simple_moving_average_five_day'] = 0;
		$output[$i]['exponential_moving_average_five_day'] = 0;
	}

// Five days Simple Moving Average Calculation
	for ( $i = (FIVE_DAYS_PERIOD - 1); $i<= (count($output) - 1); $i++) {
		$simple_moving_average_five_day = 0;
		for ( $j = $i; $j > ($i - FIVE_DAYS_PERIOD); $j--) {
			$simple_moving_average_five_day = $simple_moving_average_five_day + $output[$j]['close'];
		}
		$output[$i]['simple_moving_average_five_day'] = number_format(($simple_moving_average_five_day/FIVE_DAYS_PERIOD), 2, '.', '');
	}

// Five days Exponential Moving Average Calculation
	$output[4]['exponential_moving_average_five_day'] = $output[4]['simple_moving_average_five_day'];

	for ( $i = FIVE_DAYS_PERIOD; $i<= (count($output) - 1); $i++) {
		$exponential_moving_average_five_day = (SMOOTHNING_CONSTANT_FIVE_DAYS_PERIOD * ( $output[$i]['close'] - $output[$i - 1]['exponential_moving_average_five_day'] )) + $output[$i - 1]['exponential_moving_average_five_day'];
		$output[$i]['exponential_moving_average_five_day'] = number_format($exponential_moving_average_five_day, 2, '.', '');
	}

	/************************ Fifteen Days Calculations Start ****************************/
	for( $i = 0; $i <= (FIFTEEN_DAYS_PERIOD - 2); $i++ ) {
		$output[$i]['simple_moving_average_fifteen_day'] = 0;
		$output[$i]['exponential_moving_average_fifteen_day'] = 0;
	}
// Fifteen days Simple Moving Average Calculation
	for ( $i = (FIFTEEN_DAYS_PERIOD - 1); $i<= (count($output) - 1); $i++) {
		$simple_moving_average_fifteen_day = 0;
		for ( $j = $i; $j > ($i - FIFTEEN_DAYS_PERIOD); $j--) {
			$simple_moving_average_fifteen_day = $simple_moving_average_fifteen_day + $output[$j]['close'];
		}
		$output[$i]['simple_moving_average_fifteen_day'] = number_format(($simple_moving_average_fifteen_day/FIFTEEN_DAYS_PERIOD), 2, '.', '');
	}

// Fifteen days Exponential Moving Average Calculation
	$output[14]['exponential_moving_average_fifteen_day'] = $output[14]['simple_moving_average_fifteen_day'];

	for ( $i = FIFTEEN_DAYS_PERIOD; $i<= (count($output) - 1); $i++) {
		$exponential_moving_average_fifteen_day = (SMOOTHNING_CONSTANT_FIFTEEN_DAYS_PERIOD * ( $output[$i]['close'] - $output[$i - 1]['exponential_moving_average_fifteen_day'] )) + $output[$i - 1]['exponential_moving_average_fifteen_day'];
		$output[$i]['exponential_moving_average_fifteen_day'] = number_format($exponential_moving_average_fifteen_day, 2, '.', '');
	}

// difference between five days moving average and fifteen days moving average
	foreach ( $output as $key => $value ) {
		if ( $output[$key]['exponential_moving_average_five_day'] > 0 && $output[$key]['exponential_moving_average_fifteen_day'] > 0 ) {
			$output[$key]['difference'] = abs( $output[$key]['exponential_moving_average_five_day'] - $output[$key]['exponential_moving_average_fifteen_day'] );
		}
		else {
			$output[$key]['difference'] = 0;
		}
	}

// Insert into database
	foreach ( $output as $key => $value ) {
		$sql = "INSERT INTO " . $table ." (symbol, date_excecuted, closing_price, five_day_sma, five_day_ema, fifteen_day_sma, fifteen_day_ema, difference) 
		       VALUES('" . $symbol . "',
		              '" . date('Y-m-d', strtotime($value['date'])) . "',
		              '" . $value['close'] . "',
		              '" . $value['simple_moving_average_five_day'] . "',
		               '" . $value['exponential_moving_average_five_day'] . "',
		               '" . $value['simple_moving_average_fifteen_day'] . "',
		               '" . $value['exponential_moving_average_fifteen_day'] . "',
		               '" . $value['difference'] . "'
		              )";

		$conn->query($sql);

		//sprint $symbol . " inserted ";
	}
	/*print '<pre>';
	print_r($output);
	exit;*/
}
