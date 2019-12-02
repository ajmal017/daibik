<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "daibik";
$table = "fo_margin";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error ) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	$url = 'https://zerodha.com/margin-calculator/Futures';
	$html = file_get_contents($url);

	$margin_page = new DOMDocument();

	libxml_use_internal_errors(TRUE);

	if(!empty($html)) {
		$margin_page->loadHTML($html);

		libxml_clear_errors(); //

		$output = array();

		$margin_page_xpath = new DOMXPath($margin_page);

		// Fetching the scripts
		$script = $margin_page_xpath->query('//td[@class="scrip"]');
		if($script->length > 0 ){
			$counter = 0;
			$index = -1;
			foreach ( $script as $row ) {
				if ( ($counter%3) == 0 ) {
					$index++;
					$output[$index]['script'] = trim($row->nodeValue);
				}
				$counter++;
			}
		}

		// Fetching the lot size
		$lot = $margin_page_xpath->query('//td[@class="lot"]');
		if($lot->length > 0 ){
			$counter = 0;
			$index = -1;
			foreach ( $lot as $row ) {
				if ( ($counter%3) == 0 ) {
					$index++;
					$output[$index]['lot'] =  trim($row->nodeValue);
				}
				$counter++;
			}
		}

		// Fetching the MIS margin
		$lot = $margin_page_xpath->query('//td[@class="mis"]');
		if($lot->length > 0 ){
			$counter = 0;
			$index = -1;
			foreach ( $lot as $row ) {
				if ( ($counter%3) == 0 ) {
					$index++;
					$output[$index]['mis_margin'] =  trim($row->nodeValue);
				}
				$counter++;
			}
		}

		// Fetching the normal margin
		$lot = $margin_page_xpath->query('//td[@class="nrml"]');
		if($lot->length > 0 ){
			$counter = 0;
			$index = -1;
			foreach ( $lot as $row ) {
				if ( ($counter%3) == 0 ) {
					$index++;
					$output[$index]['nrml_margin'] =  trim($row->nodeValue);
				}
				//print trim($row->nodeValue) . "<pre>";
				$counter++;
			}
		}

		//exit;
		/*$all_data = $margin_page_xpath->query('//tr[@class=""]');
		if($all_data->length > 0 ){
			foreach ( $all_data as $row ) {
				$response = $row->nodeValue;

				$margin_script = new DOMDocument();
				libxml_use_internal_errors(TRUE);
				$margin_script->loadHTML($response);
				libxml_clear_errors();

				$margin_script_xpath = new DOMXPath($margin_script);
				$script = $margin_script_xpath->query('//td[@class="scrip"]');

				if ($script->length > 0 ){
					foreach ( $script as $row ) {
						print '<pre>';
						print_r($row);
					}
				}
			}
		}*/

		foreach ( $output as $key => $value ) {
			$sql = "INSERT INTO " . $table ." (symbol, lot_size, normal_margin, mis_margin) 
		       VALUES('" . $value['script'] . "',
		              '" . $value['lot'] . "',
		              '" . $value['nrml_margin'] . "',
		              '" . $value['mis_margin'] . "'
		              )";

			$conn->query($sql);
		}

		file_put_contents('fo_margin.json', json_encode($output));
	}
	/*print '<pre>';
	print_r($output);*/
}

?>
