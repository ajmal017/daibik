<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
define('ACCEPTED_BODY_LENGTH_RATIO', 10);
define('ACCEPTED_WICKS_RATIO', 2);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "tojo";
$table = "boomer";

$symbol = trim($_GET['symbol']);
$symbol = str_replace('_', '&', $symbol);
//$symbol = 'ITC';

print $symbol . "\r\n";
// Create connection
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ( $conn->connect_error ||  empty( $symbol )) {
    die("Connection failed: " . $conn->connect_error);
}
else {
    $csv_raw = array_map( 'str_getcsv', file( 'MODIFIED/' . $symbol . '.csv' ) );

    $csv = $info = $data = array();

    foreach ( $csv_raw as $key => $value ) {
        if ( !empty( $value[0] ) && $key > 0 ) {
            $csv[$key][0] = $value[0];
            $csv[$key][1] = trim( $value[3] ); //open
            $csv[$key][2] = trim( $value[4] ); //high
            $csv[$key][3] = trim( $value[5] ); //low
            $csv[$key][4] = trim( $value[7] ); //close
            $csv[$key][5] = trim( $value[8] ); //ltp
        }
    }

    //$csv = array_reverse( $csv );
    //$csv[count($csv)][0] = date('d-M-Y');

    //print '<pre>';
    //print_r($csv);

    $matched_date_str = $next_day_high_str = $next_day_low_str = 0;
    if ( ! empty( $csv ) ) {
        foreach ( $csv as $key => $value ) {
            if ( ! empty( $csv[$key+1][0] ) ) {
                //$current_high_low = number_format( (( $csv[$key][2] - $csv[$key][3] )/$csv[$key][3])*100, 2, '.', '' );
                //$prev_high_low = number_format( (( $csv[$key+1][2] - $csv[$key+1][3] )/$csv[$key+1][3])*100, 2, '.', '' );

                $current_body_length = abs( $csv[$key][1] - $csv[$key][4] );
                $current_wicks_length = abs( $csv[$key][2] - $csv[$key][3] );
                $current_body_to_wicks_ratio = number_format( (( $current_body_length/$current_wicks_length)*100), 2, '.', '' );

                $prev_body_length = abs( $csv[$key+1][1] - $csv[$key+1][4] );
                $prev_wicks_length = abs( $csv[$key+1][2] - $csv[$key+1][3] );
                $prev_body_to_wicks_ratio = number_format( (( $prev_body_length/$prev_wicks_length)*100), 2, '.', '' );

                /*if ( $current_high_low <= 1.6 &&
                    ( $csv[$key][1] != $csv[$key][2] ) &&  ( $csv[$key][3] != $csv[$key][4] ) &&
                    $prev_high_low <= 1.6 &&
                    ( $csv[$key+1][1] != $csv[$key+1][2] ) &&  ( $csv[$key+1][3] != $csv[$key+1][4] )
                ) {*/

                if ( $current_body_to_wicks_ratio <= 25 &&
                    ( $csv[$key][1] != $csv[$key][2] ) &&  ( $csv[$key][3] != $csv[$key][4] ) &&
                    $prev_body_to_wicks_ratio <= 25 &&
                    ( $csv[$key+1][1] != $csv[$key+1][2] ) &&  ( $csv[$key+1][3] != $csv[$key+1][4] )
                ) {

                    $matched_date_str = $csv[$key][0] . ', ' . $csv[$key+1][0] ;

                    //print '<pre>';
                    //print_r($csv[$key+1]);

                    if ( ! empty( $csv[$key-1][0] ) ) {
                        $next_day_high_str = number_format( (( $csv[$key-1][2] - $csv[$key-1][1] )/$csv[$key-1][1])*100, 2, '.', '' );
                        $next_day_low_str = number_format( (( $csv[$key-1][1] - $csv[$key-1][3] )/$csv[$key-1][1])*100, 2, '.', '' );
                    }

                    $sql = "INSERT INTO $table (symbol, matched, next_day_high, next_day_low, executed) VALUES('".
                        $symbol . "', '" .
                        $matched_date_str . "', '" .
                        $next_day_high_str . "', '" .
                        $next_day_low_str . "', '" .
                        date('Y-m-d' ). "')";

                    //print $sql . "\r\n";
                    $conn->query($sql);

                    $matched_date_str = $next_day_high_str = $next_day_low_str = 0;
                }
            }
        }
    }
}
