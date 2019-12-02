<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "tojo";
$table = "standard_deviation";

$symbol = trim($_GET['symbol']);
$symbol = str_replace('_', '&', $symbol);
//$symbol = 'COALINDIA';

//print $symbol . "\r\n";

$conn = new mysqli($servername, $username, $password, $dbname);

if (  $conn->connect_error || empty( $symbol )) {
    die("Connection failed: " . $conn->connect_error);
}
else {
    $csv_raw = array_map( 'str_getcsv', file( 'MODIFIED/' . $symbol . '.csv' ) );

    $csv = $info = $data = array();
    $arr = [];

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

    foreach ( $csv as $key => $value ) {
        if ( !empty( $value[0] ) && $key > 1 ) {
            $arr[] = number_format( ( ( ( $csv[$key][4]/$csv[$key-1][4] ) - 1)*100 ), 2, '.', '');
        }
    }

    $standard_deviation = Stand_Deviation( $arr );
    if ( $standard_deviation > 0 ) {
        $standard_deviation = number_format( $standard_deviation, 2, '.', '');

        $sql = "INSERT INTO $table (symbol, `value`, executed) VALUES('".
            $symbol . "', " .
            $standard_deviation . ", '" .
            date('Y-m-d' ). "')";

        //print $sql. "<br />";

        $conn->query($sql);
    }
    print $standard_deviation . "<br />";
}

// function to calculate the standard deviation
// of array elements
function Stand_Deviation($arr) {
    $num_of_elements = count($arr);

    $variance = 0.0;

    // calculating mean using array_sum() method
    $average = array_sum($arr)/$num_of_elements;

    foreach($arr as $i)
    {
        // sum of squares of differences between
        // all numbers and means.
        $variance += pow(($i - $average), 2);
    }

    return (float)sqrt($variance/$num_of_elements);
}
