<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ATR_PERIOD', 14);
define('MULTIPLIER', 2);

$symbol = trim($_GET['symbol']);
$url = 'http://localhost/daibik/FO_CSV/'. $symbol .'.csv';
$csv = array_map('str_getcsv', file($url));

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

$input = array_slice($input, 2);
$input = array_reverse($input);

/*print '<pre>';
print_r($input);
exit;*/

$output = array();
$counter = 0;
// True Range Calculation
foreach ( $input as $key => $value ) {
	$output[$counter]['date'] = $value[0];
	$output[$counter]['open'] = $value[3];
	$output[$counter]['high'] = $value[4];
	$output[$counter]['low'] = $value[5];
	$output[$counter]['close'] = $value[7];

	$high_low = $value[4] - $value[5];
	$high_close = abs($value[4] - $value[7]);
	$low_close = abs($value[5] - $value[7]);
	$max = max($high_low, $high_close, $low_close);

	$output[$counter]['true_range'] = $max;

	$counter++;
}

/*print '<pre>';
print_r($output);
exit;*/

for( $i = 0; $i <= (ATR_PERIOD - 2); $i++ ) {
	$output[$i]['average_true_range'] = 0;
	$output[$i]['basic_upper_band'] = 0;
	$output[$i]['basic_lower_band'] = 0;
	$output[$i]['final_upper_band'] = 0;
	$output[$i]['final_lower_band'] = 0;
	$output[$i]['supertrend'] = 0;
	$output[$i]['signal'] = 0;
}



// Average True Range Calculation
$output[(ATR_PERIOD - 1)]['average_true_range'] = 0;
for ( $i = (ATR_PERIOD - 1); $i >=0; $i--) {
	$output[(ATR_PERIOD - 1)]['average_true_range'] = $output[(ATR_PERIOD - 1)]['average_true_range'] + $output[$i]['true_range'];
}

$output[(ATR_PERIOD - 1)]['average_true_range'] = number_format(($output[(ATR_PERIOD - 1)]['average_true_range']/ATR_PERIOD), 2, '.', '');

for ( $i = ATR_PERIOD; $i <= (count($output) - 1); $i++) {
	$next_atr = ($output[$i - 1]['average_true_range'] * ( ATR_PERIOD - 1 ) + $output[$i]['true_range'])/ATR_PERIOD;
	$output[$i]['average_true_range'] = number_format($next_atr, 2, '.', '');
}

// Basic Upper Band and Lower Band Calculation
for ( $i = (ATR_PERIOD - 1); $i <= (count($output) - 1); $i++) {
	$output[$i]['basic_upper_band'] = number_format(($output[$i]['high'] + $output[$i]['low']) / 2, 2, '.', '') + ( MULTIPLIER * $output[$i]['average_true_range']);
	$output[$i]['basic_lower_band'] = number_format(($output[$i]['high'] + $output[$i]['low']) / 2, 2, '.', '') - ( MULTIPLIER * $output[$i]['average_true_range']);
}



// Final Upper Band Calculation
for ( $i = (ATR_PERIOD - 1); $i <= (count($output) - 1); $i++) {
  if ( $i == (ATR_PERIOD - 1) ) {
	  $output[$i]['final_upper_band'] = $output[$i]['basic_upper_band'];
  }
  else {
	  if ( ($output[$i]['basic_upper_band'] < $output[$i - 1]['final_upper_band']) || ($output[$i - 1]['close'] > $output[$i - 1]['final_upper_band']) ) {
		  $output[$i]['final_upper_band'] = $output[$i]['basic_upper_band'];
	  }
	  else {
		  $output[$i]['final_upper_band'] = $output[$i - 1]['final_upper_band'];
	  }
  }
}

// Final Lower Band Calculation
for ( $i = (ATR_PERIOD - 1); $i <= (count($output) - 1); $i++) {
  if ( $i == (ATR_PERIOD - 1) ) {
	  $output[$i]['final_lower_band'] = $output[$i]['basic_lower_band'];
  }
  else {
	  if ( ($output[$i]['basic_lower_band'] > $output[$i - 1]['final_lower_band']) || ($output[$i - 1]['close'] < $output[$i - 1]['final_lower_band']) ) {
		  $output[$i]['final_lower_band'] = $output[$i]['basic_lower_band'];
	  }
	  else {
		  $output[$i]['final_lower_band'] = $output[$i - 1]['final_lower_band'];
	  }
  }
}

// Supertrend Calculation
for ( $i = (ATR_PERIOD - 1); $i <= (count($output) - 1); $i++) {
	if ( $output[$i]['close'] < $output[$i]['final_upper_band'] ) {
		$output[$i]['supertrend'] = $output[$i]['final_upper_band'];
		$output[$i]['signal'] = 'Buy';
	}
	else {
		$output[$i]['supertrend'] = $output[$i]['final_lower_band'];
		$output[$i]['signal'] = 'Sell';
	}
}
//print '<pre>';
//print_r($output);
?>
<html>
<head>
	<title>Testing</title>
</head>
<body>
<table border="1">
	<tr>
		<th>Date</th>
		<th>Open</th>
		<th>high</th>
		<th>Low</th>
		<th>Close</th>
		<th>True Range</th>
		<th>Avg True Range</th>
		<th>Basic Upper Band</th>
		<th>Basic Lower Band</th>
		<th>Final Upper Band</th>
		<th>Final Lower Band</th>
		<th>Supertrend</th>
		<!--<th>Signal</th>-->
	</tr>

		<?php
		  foreach ( $output as $key => $value ) {
        ?>
	<tr>
			  <td><?php print  $value['date']; ?></td>
			  <td><?php print  $value['open']; ?></td>
			  <td><?php print  $value['high']; ?></td>
			  <td><?php print  $value['low']; ?></td>
			  <td><?php print  $value['close']; ?></td>
			  <td><?php print  $value['true_range']; ?></td>
			  <td><?php print  $value['average_true_range']; ?></td>
			  <td><?php print  $value['basic_upper_band']; ?></td>
			  <td><?php print  $value['basic_lower_band']; ?></td>
			  <td><?php print  $value['final_upper_band']; ?></td>
			  <td><?php print  $value['final_lower_band']; ?></td>
			  <td><?php print  $value['supertrend']; ?></td>
			  <!--<td><?php //print  $value['signal']; ?></td>-->
	</tr>
		<?php
		  }
		?>
</table>
</body>
</html>
