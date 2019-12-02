<?php

$symbol = trim($_GET['symbol']);

$csv = array_map( 'str_getcsv', file( 'MODIFIED/' . $symbol . '.csv' ) );

$fp = fopen('matched.csv', 'a+');
foreach ( $csv as $key => $value ) {
	if ( $key > 0 ) {
		if ( trim( $value[1] ) == trim( $value[3] )) {
			fputcsv($fp, array($symbol, $value[0]));
		}
	}
}
fclose($fp);
print $symbol . " updated\r\n";
?>
