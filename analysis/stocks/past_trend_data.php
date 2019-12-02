<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "surya";

$symbol = trim($_GET['symbol']);
$symbol = str_replace('_', '&', $symbol);
//$symbol = 'IOC';

print $symbol . "\r\n";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error || empty( $symbol )) {
	die("Connection failed: " . $conn->connect_error);
}
else {
  $data = [];
  /*$live_feed_sql = "SELECT * FROM `live_feed_stocks` WHERE symbol='" . $symbol . "'";
  $query=mysqli_query($conn,$live_feed_sql);
  $result = mysqli_fetch_array($query,MYSQLI_ASSOC);
  $past_trend_continuation = $result['past_trend_continuation'];*/

  /*print '<pre>';
  print_r($result);*/

  /*$average_of_average_volume = 0;
  $volume_sql = "SELECT * FROM `volume_analysis_stocks` WHERE symbol='" . $symbol . "' ORDER BY executed DESC LIMIT 0,". $past_trend_continuation;
  $query=mysqli_query($conn,$volume_sql);
  while ( $result = mysqli_fetch_array($query,MYSQLI_ASSOC) ) {
	  $average_of_average_volume =  $average_of_average_volume + $result['total_percentage_volume'];
  }
  $average_of_average_volume = number_format(($average_of_average_volume/$past_trend_continuation), 2, '.', '');

  print $average_of_average_volume;*/

  $volume_sql = "SELECT * FROM `volume_analysis_stocks` WHERE symbol='" . $symbol . "' ORDER BY executed DESC";
  $query=mysqli_query($conn,$volume_sql);
  while ( $result = mysqli_fetch_array($query,MYSQLI_ASSOC) ) {
	  $data[] =  $result;
  }

  $type = '';
  $continuation = 0;
  $average_of_average_volume = 0;
  $length = count( $data );
  foreach ( $data as $key => $value ) {
		if ( ($key+1) < $length ) {
			if ( ( $data[$key]['average_volume'] > $data[$key+1]['average_volume'] ) &&
			     ( $data[$key]['closing_price'] <= $data[$key+1]['closing_price'] ) &&
			     ( $type == '' || $type == 'Volume Increase Price Decrease' )
			) {
				$type = 'Volume Increase Price Decrease';
				$average_of_average_volume =  $average_of_average_volume + $data[$key]['total_percentage_volume'];
				$continuation++;
			}
			elseif ( ( $data[$key]['average_volume'] > $data[$key+1]['average_volume'] ) &&
			         ( $data[$key]['closing_price'] >= $data[$key+1]['closing_price'] ) &&
			         ( $type == '' || $type == 'Volume Increase Price Increase' )
			) {
				$type = 'Volume Increase Price Increase';
				$average_of_average_volume =  $average_of_average_volume + $data[$key]['total_percentage_volume'];
				$continuation++;
			}
			else if ( ( $data[$key]['average_volume'] < $data[$key+1]['average_volume'] ) &&
			          ( $data[$key]['closing_price'] <= $data[$key+1]['closing_price'] ) &&
			          ( $type == '' || $type == 'Volume Decrease Price Decrease' )
			) {
				$type = 'Volume Decrease Price Decrease';
				$average_of_average_volume =  $average_of_average_volume + $data[$key]['total_percentage_volume'];
				$continuation++;
			}
			else if ( ( $data[$key]['average_volume'] < $data[$key+1]['average_volume'] ) &&
			          ($data[$key]['closing_price'] >= $data[$key+1]['closing_price'] ) &&
			          ( $type == '' || $type == 'Volume Decrease Price Increase' )
			) {
				$type = 'Volume Decrease Price Increase';
				$average_of_average_volume =  $average_of_average_volume + $data[$key]['total_percentage_volume'];
				$continuation++;
			}
			else {
				$average_of_average_volume =  $average_of_average_volume + $data[$key]['total_percentage_volume'];
				$average_of_average_volume = number_format(($average_of_average_volume/($continuation+1)), 2, '.', '');
				//print 'Type = ' . $type . "<br />";
				//print 'Continuation = ' . $continuation . "<br />";
				//print 'Average of Average Volume Percentage = ' . $average_of_average_volume . "<br />";
				break;
			}
		}
  }

  $update_sql = "UPDATE `live_feed_stocks` 
          SET past_trend_name='" . $type . "', 
          past_trend_average_volume=" . $average_of_average_volume . ", 
          past_trend_continuation=" . $continuation . " WHERE symbol='". $symbol ."'";

  $conn->query($update_sql);

  /*print '<pre>';
  print_r($data);*/
}
