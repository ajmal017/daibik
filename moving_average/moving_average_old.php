<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('FIVE_DAYS_PERIOD', 5);
define('FIFTEEN_DAYS_PERIOD', 15);
define('SMOOTHNING_CONSTANT_FIVE_DAYS_PERIOD', 2/(FIVE_DAYS_PERIOD + 1));
define('SMOOTHNING_CONSTANT_FIFTEEN_DAYS_PERIOD', 2/(FIFTEEN_DAYS_PERIOD + 1));

$file = 'TCS_OLD.csv';
print $file . '<br />';
$csv = array_map('str_getcsv', file($file));

//print '<pre>';
//print_r($csv);

foreach ( $csv as $key => $value ) {
	if ( $key > 0 ) {
		foreach ( $value as $key1 => $value1 ) {
			$input[$key - 1][$key1] = trim( $value1 );
	    }
	}
}

$output = array();
$counter = 0;
foreach ( $input as $key => $value ) {
	$output[$counter]['date'] = $value[2];
	$output[$counter]['close'] = $value[8];
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

?>
<html>
<head>
	<title>Testing</title>
</head>
<body>
<table border="1">
	<tr>
		<th>Count</th>
		<th>Date</th>
		<th>Closing Price</th>
		<!--<th><?php //print FIVE_DAYS_PERIOD; ?> Day SMA</th>-->
		<th><?php print FIVE_DAYS_PERIOD; ?> Day EMA</th>
		<!--<th><?php //print FIFTEEN_DAYS_PERIOD; ?> Day SMA</th>-->
		<th><?php print FIFTEEN_DAYS_PERIOD; ?> Day EMA</th>
		<th>Difference</th>
	</tr>
	<?php
	foreach ( $output as $key => $value ) {
		?>
		<tr>
			<td><?php print $key + 1; ?></td>
			<td><?php print $value['date']; ?></td>
			<td><?php print $value['close']; ?></td>
			<!--<td><?php //print $value['simple_moving_average_five_day']; ?></td>-->
			<td><?php print $value['exponential_moving_average_five_day']; ?></td>
			<!--<td><?php //print $value['simple_moving_average_fifteen_day']; ?></td>-->
			<td><?php print $value['exponential_moving_average_fifteen_day']; ?></td>
			<td><?php print abs($value['exponential_moving_average_five_day'] - $value['exponential_moving_average_fifteen_day']); ?></td>
		</tr>
		<?php
	}
	?>
</table>
</body>
</html>
