<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "daibik";
$table = "stocks_movement";

$symbol = trim($_GET['symbol']);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error || empty( $symbol )) {
	die("Connection failed: " . $conn->connect_error);
}
else {

	$url = 'http://localhost/daibik/downloads/'. $symbol .'.html';
	$html = file_get_contents($url);


	$stock_page = new DOMDocument();

	libxml_use_internal_errors(TRUE); //disable libxml errors

	if(!empty($html)) {
		$stock_page->loadHTML($html);

		libxml_clear_errors(); //

		$stock_page_xpath = new DOMXPath($stock_page);

		$current_price = $stock_page_xpath->query('//div[@id="responseDiv"]');

		if($current_price->length > 0 ){
			foreach($current_price as $row){
				//print $row->nodeValue;

				$response = json_decode($row->nodeValue);

				$buyquantity = str_replace(',', '', $response->data[0]->totalBuyQuantity);
				$sellquantity = str_replace(',', '', $response->data[0]->totalSellQuantity);
				$current_price = $response->data[0]->lastPrice;
			}
		}


		if ( $buyquantity > $sellquantity ) {
			$difference = $buyquantity - $sellquantity;
			$direction = 1;
		}
		elseif ( $buyquantity < $sellquantity ) {
			$difference = $sellquantity - $buyquantity;
			$direction = 2;
		}

		$sql = "INSERT INTO " . $table ." (symbol, current_price, difference, direction, executed) 
		       VALUES('" . $symbol . "',
		              '" . $current_price . "',
		              '" . $difference . "',
		              '" . $direction . "',
		              NOW()
		              )";

		$conn->query($sql);

		echo "$symbol updated\r\n";
	}
}


?>
