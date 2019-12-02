<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "surya";
$table = "live_banknifty";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error ) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	//$csv_raw = array_map( 'str_getcsv', file( 'downloads/banknifty.json' ) );

	$data = json_decode( file_get_contents( 'downloads/banknifty.json' ) );

	//print '<pre>';
	//print_r($data);

	$totalBuyQuantity = str_replace(',', '', $data->data[0]->totalBuyQuantity);
	$totalSellQuantity = str_replace(',', '', $data->data[0]->totalSellQuantity);
	$lastPrice = str_replace(',', '', $data->data[0]->lastPrice);
	$difference = $totalBuyQuantity - $totalSellQuantity;

	print 'Total Buy Quantity = ' . $totalBuyQuantity . "\r\n";
	print 'Total Sell Quantity = ' . $totalSellQuantity . "\r\n";
	print 'Last Price = ' . $lastPrice . "\r\n";
	print 'Difference = ' . $difference . "\r\n";

	if ( $lastPrice > 0 ) {
		$sql = "INSERT INTO " . $table ." (last_price, total_buy_quatity, total_sell_quatity, difference, executed)
		       VALUES('" . $lastPrice . "',
		              '" . $totalBuyQuantity . "',
		              '" . $totalSellQuantity . "',
		              '" . $difference . "',
		              NOW()
		              )";

		$conn->query($sql);
	}

}

?>
