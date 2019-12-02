<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "daibik";
$table = "news";

$symbol = trim($_GET['symbol']);
$url    = trim($_GET['url']);
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error || empty( $symbol )) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	//print 'url = ' . $url . "\r\n";
	//print 'symbol = ' . $symbol . "\r\n";
	//$url = 'http://www.moneycontrol.com/india/stockpricequote/finance-leasing-hire-purchase/bajajfinance/BAF';
	$html = file_get_contents($url);
	$info = array();

	$stock_page = new DOMDocument();

	libxml_use_internal_errors(TRUE); //disable libxml errors

	if(!empty($html)) {
		$stock_page->loadHTML($html);

		libxml_clear_errors(); //

		$stock_page_xpath = new DOMXPath($stock_page);

		//$news = $stock_page_xpath->query('//ul[@class="mrkt_action_list"]');

		$count = 0;
		$news = $stock_page_xpath->query('//div[@class="list_desc FL"]/a[@class="bl_12"]');
		if($news->length > 0 ) {
			foreach ( $news as $row ) {
				//print '<pre>';
				//print_r($row->nodeValue);
				$info[$count]['news'] = trim( $row->nodeValue );
				$count++;
			}
		}

		$count = 0;
		$timestamp = $stock_page_xpath->query('//span[@class="timestamp"]');
		if( $timestamp->length > 0 ) {
			foreach ( $timestamp as $row ) {
				//print '<pre>';
				//print_r($row->nodeValue);
				$info[$count]['timestamp'] = trim( $row->nodeValue );
				$count++;
			}
		}

		//print '<pre>';
		//print_r($info);

		foreach ( $info as $key => $value ) {
			$sql = "INSERT INTO " . $table ." (symbol, info, executed) 
		       VALUES('" . $symbol . "',
		              '" . $value['news'] . "',
		              '" . date('Y-m-d H:i:s', strtotime($value['timestamp'])) . "'
		              )";

			//print $sql . '<br />';
			$conn->query($sql);
		}
	}
}

