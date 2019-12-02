<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "daibik";
$table = "gainers_loosers";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error ) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	//$data = '{"time":"Apr 12, 2018 16:00:00","data":[{"symbol":"DISHTV","series":"EQ","openPrice":"75.50","highPrice":"82.35","lowPrice":"74.70","ltp":"78.85","previousPrice":"73.95","netPrice":"6.63","tradedQuantity":"3,90,28,071","turnoverInLakhs":"30,359.94","lastCorpAnnouncementDate":"21-Sep-2017","lastCorpAnnouncement":"Annual General Meeting"},{"symbol":"HCLTECH","series":"EQ","openPrice":"967.95","highPrice":"1,013.00","lowPrice":"967.30","ltp":"1,007.00","previousPrice":"967.30","netPrice":"4.10","tradedQuantity":"40,91,383","turnoverInLakhs":"41,046.39","lastCorpAnnouncementDate":"29-Jan-2018","lastCorpAnnouncement":"Interim Dividend - Rs 2 Per Share (Purpose Revised)"},{"symbol":"TCS","series":"EQ","openPrice":"3,010.00","highPrice":"3,150.00","lowPrice":"3,007.95","ltp":"3,136.00","previousPrice":"3,014.15","netPrice":"4.04","tradedQuantity":"30,57,180","turnoverInLakhs":"95,270.29","lastCorpAnnouncementDate":"22-Jan-2018","lastCorpAnnouncement":"Interim Dividend - Rs 7 Per Share"},{"symbol":"INFY","series":"EQ","openPrice":"1,129.45","highPrice":"1,172.75","lowPrice":"1,125.00","ltp":"1,164.05","previousPrice":"1,124.25","netPrice":"3.54","tradedQuantity":"85,22,183","turnoverInLakhs":"98,661.31","lastCorpAnnouncementDate":"31-Oct-2017","lastCorpAnnouncement":"Interim Dividend - Rs 13\/- Per Share (Purpose Revised)"},{"symbol":"NIITTECH","series":"EQ","openPrice":"896.00","highPrice":"926.50","lowPrice":"893.60","ltp":"918.00","previousPrice":"891.00","netPrice":"3.03","tradedQuantity":"24,63,298","turnoverInLakhs":"22,595.83","lastCorpAnnouncementDate":"14-Sep-2017","lastCorpAnnouncement":"Annual General Meeting\/Dividend - Rs 12.50 Per Share"},{"symbol":"HAVELLS","series":"EQ","openPrice":"530.00","highPrice":"551.00","lowPrice":"527.70","ltp":"546.95","previousPrice":"530.85","netPrice":"3.03","tradedQuantity":"22,91,213","turnoverInLakhs":"12,480.01","lastCorpAnnouncementDate":"21-Jun-2017","lastCorpAnnouncement":"Annual General Meeting\/Dividend - Rs 3.50 Per Share"},{"symbol":"TECHM","series":"EQ","openPrice":"636.40","highPrice":"655.00","lowPrice":"634.05","ltp":"652.55","previousPrice":"633.35","netPrice":"3.03","tradedQuantity":"36,14,281","turnoverInLakhs":"23,415.84","lastCorpAnnouncementDate":"27-Jul-2017","lastCorpAnnouncement":"Annual General Meeting\/ Dividend -Rs 9\/- Per Share"},{"symbol":"GODREJIND","series":"EQ","openPrice":"560.70","highPrice":"579.30","lowPrice":"558.10","ltp":"575.30","previousPrice":"559.40","netPrice":"2.84","tradedQuantity":"15,41,483","turnoverInLakhs":"8,790.92","lastCorpAnnouncementDate":"02-Aug-2017","lastCorpAnnouncement":"Dividend - Rs 1.75 Per Share"},{"symbol":"TATAELXSI","series":"EQ","openPrice":"1,042.50","highPrice":"1,079.65","lowPrice":"1,041.00","ltp":"1,072.00","previousPrice":"1,042.60","netPrice":"2.82","tradedQuantity":"13,60,731","turnoverInLakhs":"14,550.57","lastCorpAnnouncementDate":"18-Sep-2017","lastCorpAnnouncement":"Bonus 1:1"},{"symbol":"APOLLOTYRE","series":"EQ","openPrice":"288.50","highPrice":"297.20","lowPrice":"284.30","ltp":"294.50","previousPrice":"286.75","netPrice":"2.70","tradedQuantity":"40,40,620","turnoverInLakhs":"11,809.12","lastCorpAnnouncementDate":"27-Jun-2017","lastCorpAnnouncement":"Annual General Meeting\/Dividend - Rs 3\/- Per Share"}]}';

	//$data = json_decode($data);

	$data = json_decode(trim($_GET['data']));
	$type = trim($_GET['type']);

	/*print $type . "\r\n";
	print_r($data);
	print "\r\n";*/

	if ( isset( $data->data ) && !empty( $data->data )) {
		foreach ( $data->data as $key => $value ) {
			$sql = "INSERT INTO " . $table ." (symbol, rank, `type`, executed) 
		       VALUES('" . $value->symbol . "',
		              '" . ( $key + 1 ) . "',
		              '" . $type . "',
		              NOW()
		              )";

			//print $sql . "\r\n";
			$conn->query($sql);

			print "type = " . $type  . " " . $value->symbol . " added \r\n";
		}
	}
}
