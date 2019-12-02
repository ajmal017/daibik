<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(-1);

define('FIVE_DAYS_PERIOD', 5);
define('FIFTEEN_DAYS_PERIOD', 15);
define('SMOOTHNING_CONSTANT_FIVE_DAYS_PERIOD', 2/(FIVE_DAYS_PERIOD + 1));
define('SMOOTHNING_CONSTANT_FIFTEEN_DAYS_PERIOD', 2/(FIFTEEN_DAYS_PERIOD + 1));

define('FIRST_TRADED_DAY', strtotime('01-Nov-2018'));
define('LAST_TRADED_DAY', strtotime('30-Nov-2018'));
?>

<html>
<head>
	<title>Testing</title>
</head>
<body>
<table border="1">
	<tr>
		<th>Symbol</th>
		<th><?php print FIVE_DAYS_PERIOD; ?> Day EMA</th>
		<th><?php print FIFTEEN_DAYS_PERIOD; ?> Day EMA</th>
		<th>Difference</th>
		<th>Type</th>
	</tr>

<?php
$dir = "downloads/";
$all_symbols = [];
// Open a directory, and read its contents
if (is_dir($dir)){
	if ($dh = opendir($dir)){
		while (($file = readdir($dh)) !== false ){
			if ( $file != "." && $file != ".."  && $file != ".DS_Store" ) {
				//echo "filename:" . $file . "<br>";
				$all_symbols[] = explode('.', $file)[0];
			}

		}
		closedir($dh);
	}
}

/*print '<pre>';
print_r($all_symbols);

exit;*/

foreach ( $all_symbols as $key => $symbol ) {
	//$symbol = 'GRASIM';
    //print $symbol . '<br />';
	$csv = array_map( 'str_getcsv', file( 'MODIFIED/' . $symbol . '.csv' ) );

	/*print '<pre>';
	print_r($csv);
	exit;*/

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

	$input = array_slice($input, 1);
	$input = array_reverse($input);

// Extract the data of the last month.
	$input_last_month = [];
	foreach ( $input as $key => $value ) {
		$current_date = strtotime($value[0]);
		if ( $current_date >= FIRST_TRADED_DAY && $current_date <= LAST_TRADED_DAY ) {
			$input_last_month[] = $value;
		}
	}

//print '<pre>';
//print_r($input_last_month);

	/***********************Calculation of Fibonacci pivot point******************/
// Find out the high, low, close of the last month.
	$high = $low = $close = 0;
	foreach ( $input_last_month as $key => $value ) {
		if ( $key == 0 ) {
			$high = $value[4];
			$low = $value[5];
		}

		if ( $key > 0 && $value[4] > $high ) {
			$high = $value[4];
		}
		if ( $key > 0 &&  $value[5] < $low ) {
			$low = $value[5];
		}
	}

	$close = $input_last_month[count($input_last_month) - 1][7];

//print $high . '  ' . $low . '   ' . $close . "<br />";

	$pp = number_format(($high+$low+$close)/3, 2, '.', '');
//print $pp . "<br />";

	$r1= $pp + (( $high - $low ) * .382);
	$r2= $pp + (( $high - $low ) * .618);
	$r3= $pp + (( $high - $low ) * 1);

	$s1= $pp - (( $high - $low ) * .382);
	$s2= $pp - (( $high - $low ) * .618);
	$s3= $pp - (( $high - $low ) * 1);

//print $s1 . '===' . $s2 . '===' . $s3 . '===' . $r1 . '===' . $r2 . '===' . $r3 . "<br />";
	/*******************Calculation of Fibonacci pivot point*********************/

	/*******************Latest Data****************************/
	$json = file_get_contents('downloads/' . $symbol . '.json');
	$latest_open_price = $latest_high_price = $latest_low_price = $latest_close_price = $quantity_traded = 0;
	$candle_type = '';
	if(!empty($json)) {
		$latest_data = json_decode( $json );
		$date = date('d-M-Y');
		$latest_open_price = $latest_data->data[0]->open;
		$latest_high_price = $latest_data->data[0]->dayHigh;
		$latest_low_price = $latest_data->data[0]->dayLow;
		$latest_close_price = $latest_data->data[0]->lastPrice;
		$quantity_traded = $latest_data->data[0]->quantityTraded;

		if ( $latest_open_price > $latest_close_price ) {
			$candle_type = 'bearish';
		}
		else if ( $latest_open_price < $latest_close_price ) {
			$candle_type = 'bullish';
		}
		else {
			$candle_type = 'dozi';
		}
	}
//print '<pre>';
//print_r($latest_data);
//print $latest_open_price . '===' . $latest_high_price . '===' . $latest_low_price . '===' . $latest_close_price . "<br />";
//print 'Candle Type = ' . $candle_type;


	$latest_index = count($input);
	$input[$latest_index][0] = $date;
	$input[$latest_index][1] = $symbol;
	$input[$latest_index][2] = 'EQ';
	$input[$latest_index][3] = $latest_open_price;
	$input[$latest_index][4] = $latest_high_price;
	$input[$latest_index][5] = $latest_low_price;
	$input[$latest_index][6] = $latest_close_price;
	$input[$latest_index][7] = $latest_close_price;
	$input[$latest_index][8] = $quantity_traded;
	$input[$latest_index][9] = 0;
	/*******************Latest Data****************************/

//print '<pre>';
//print_r($input);

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
	/*print '<pre>';
	print_r($output);
	exit;*/

	  //print count($output);
	  //foreach ( $output as $key => $value ) {
	?>
	<tr>
		<td><?php print $symbol; ?></td>
		<td><?php print $output[count($output) - 1]['exponential_moving_average_five_day']; ?></td>
		<td><?php print $output[count($output) - 1]['exponential_moving_average_fifteen_day']; ?></td>
		<td><?php print $output[count($output) - 1]['exponential_moving_average_five_day'] - $output[count($output) - 1]['exponential_moving_average_fifteen_day']; ?></td>
		<td><?php print $candle_type; ?></td>
	</tr>
	<?php
	  //}
}
	?>
</table>
</body>
</html>
