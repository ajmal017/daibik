<?php
date_default_timezone_set("Asia/Kolkata");
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
    $stock_info = $stock_pattern = [];
    $index      = 0;
    $prev_symbol = '';
    $flow        = '';
    $color       = '';

    $stocks = "'TVSMOTOR','COALINDIA','M&M','VEDL','MARUTI','TATASTEEL','PFC','ULTRACEMCO','SBIN'";
    $sql = "SELECT * FROM $table WHERE symbol IN($stocks) ORDER BY symbol ASC, executed ASC";

    $result = $conn->query($sql);
    while ( $row = $result->fetch_array(MYSQLI_ASSOC) ) {
        if ( $prev_symbol !== $row['symbol'] ) {
            $index = 0;
        }
        $stock_info[$row['symbol']]['percentage'][$index] = $row['percentage'];
        $prev_symbol = $row['symbol'];
        $index++;
    }

    foreach ( $stock_info as $symbol => $info ) {
        $flow = '';
        foreach ( $info['percentage'] as $key => $value ) {
            if ( 0 === $key ) {
                $flow .= $info['percentage'][$key];
                continue;
            }
            $color = ( $info['percentage'][$key] < $info['percentage'][$key-1] ) ? 'red' : 'green';
            $flow  .= '<span style="font-weight:bold; color: ' . $color .'"> --> ' . $info['percentage'][$key] . '</span>';
        }
        $stock_pattern[$symbol] = substr( $flow, 0, ( strlen($flow) - 4 ) );
    }
}
?>
<table width="60%" border="1px;">
    <?php foreach ( $stock_pattern as $symbol => $value ) {  ?>
      <tr>
          <td colspan="3"><?php print $symbol; ?></td>
          <td><?php print $value; ?></td>
      </tr>
    <?php  } ?>
</table>
