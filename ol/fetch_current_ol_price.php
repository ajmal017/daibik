<?php

$url = 'https://www.nseindia.com/live_market/dynaContent/live_watch/get_quote/GetQuoteFO.jsp?underlying=UJJIVAN&instrument=FUTSTK&expiry=28JUN2018';
$html = file_get_contents($url);


$stock_page = new DOMDocument();

libxml_use_internal_errors(TRUE); //disable libxml errors

if(!empty($html)) {
	$stock_page->loadHTML($html);

	libxml_clear_errors(); //

	$stock_page_xpath = new DOMXPath($stock_page);

	$current_price = $stock_page_xpath->query('//div[@id="responseDiv"]');

    if($current_price->length > 0 ) {
	    foreach ( $current_price as $row ) {
		    $response = json_decode($row->nodeValue);

		    print '<pre>';
		    print_r($response);
	    }
    }
}

?>
