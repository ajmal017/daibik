<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
define('ACCEPTED_BODY_LENGTH_RATIO', 10);
define('ACCEPTED_WICKS_RATIO', 2);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "tojo";
$table = "pre_open";

// Create connection
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ( $conn->connect_error ) {
    die("Connection failed: " . $conn->connect_error);
}
else {
    $content = file_get_contents('fo.json');
    $content_decoded = json_decode( $content );

    //print '<pre>';
    //print_r($content_decoded);

    foreach ( $content_decoded->data as $key => $value ) {
        $symbol = $value->symbol;
        $percentage_diff = $value->perChn;

        print $key . '  ' . $symbol . "\r\n";
        $sql = "INSERT INTO $table (symbol, percentage, executed) VALUES('".
            $symbol . "', " .
            $percentage_diff . ", '" .
            date('Y-m-d H:i:s' ). "')";

        $conn->query($sql);
    }
}
