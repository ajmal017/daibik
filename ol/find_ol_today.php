<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "daibik";
$table = "next_match";

$symbol = trim($_GET['symbol']);
//$symbol = 'HCLTECH';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error || empty( $symbol )) {
	die("Connection failed: " . $conn->connect_error);
}
else {

	$url = 'http://localhost/daibik/ol/DOWNLOADS/'. $symbol .'.html';
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

				$open_price = trim( $response->data[0]->openPrice );
				$low_price = trim( $response->data[0]->lowPrice );

				//$open_price = 100;
				//$low_price = 100;


				//print 'Open Price = ' . $response->data[0]->openPrice;
				//print 'Low Price = ' . $response->data[0]->lowPrice;

				//print '<pre>';
				//print_r($response);

				if ( $low_price == $open_price ) {
                   $sql = "UPDATE $table SET 
                                             today_ol_appeared=1,
                                             executed=NOW() 
                                             WHERE symbol='" . $symbol . "'";
				   $conn->query($sql);
				}
				else {
					$sql = "UPDATE $table SET 
                                             today_ol_appeared=0,
                                             executed=NOW()  
                                             WHERE symbol='" . $symbol . "'";
					$conn->query($sql);
				}

			}
		}
		

		//echo "$symbol updated\r\n";
	}
}


?>
