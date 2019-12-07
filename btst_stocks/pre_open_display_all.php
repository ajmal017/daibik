<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "tojo";
$table = "pre_open_all";

$symbol = str_replace('_', '&', $_GET['symbol']);
print $symbol . '<br />';
// Create connection
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ( $conn->connect_error || empty( $symbol )) {
    die("Connection failed: " . $conn->connect_error);
}
else {
    $index      = 0;
    $stock_info = [];
    $color       = '';
    $flow        = '';

    $sql = "SELECT symbol, date(executed) as date_executed, GROUP_CONCAT(percentage) as info FROM pre_open_all  WHERE symbol='". $symbol ."' GROUP BY date(executed)";

    $result = $conn->query($sql);
    while ( $row = $result->fetch_array(MYSQLI_ASSOC) ) {
        $stock_info[$index]['date_executed'] = $row['date_executed'];
        $info_arr = explode(',', $row['info']);
        foreach ( $info_arr as $key => $value ) {
            if ( 0 === $key ) {
                $flow .= $value;
                continue;
            }
            $color = ( $info_arr[$key] < $info_arr[$key-1] ) ? 'red' : 'green';
            $flow  .= '<span style="font-weight:bold; color: ' . $color .'"> --> ' . $info_arr[$key] . '</span>';
        }
        $stock_info[$index]['info'] = $flow;
        $flow                       = '';
        $index++;
    }

    $color  = '<span style="font-weight:bold; color: ' . $color .'"> --> </span>';
    /*print '<pre>';
    print_r($stock_info);
    exit;*/
}
?>
<table width="60%" border="1px;">
    <?php foreach ( $stock_info as $skey => $value ) {  ?>
        <tr>
            <td colspan="3"><?php print $value['date_executed']; ?></td>
            <td><?php print $value['info']; ?></td>
        </tr>
    <?php  } ?>
</table>
