<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "daibik";
$gainers_loosers = "gainers_loosers";
$fo_margin = "fo_margin";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error ) {
	die("Connection failed: " . $conn->connect_error);
}
else {
	$disticnt_symbol_sql = "SELECT 
                            DISTINCT(symbol) AS symbol
                            FROM $gainers_loosers ORDER BY $gainers_loosers.executed DESC";
	$disticnt_symbol_query=mysqli_query($conn, $disticnt_symbol_sql);
	$counter = 0;
    while ( $disticnt_symbol_result = mysqli_fetch_array($disticnt_symbol_query,MYSQLI_ASSOC) ) {
	       $sql = "SELECT MAX($gainers_loosers.id),
                   $gainers_loosers.symbol, 
                   $gainers_loosers.type, 
                   $gainers_loosers.rank, 
                   $gainers_loosers.executed,
                   $fo_margin.lot_size,
                   $fo_margin.normal_margin,
                   $fo_margin.mis_margin
                   
            FROM $gainers_loosers 
            INNER JOIN $fo_margin ON $fo_margin.symbol = $gainers_loosers.symbol
            WHERE $gainers_loosers.id = (SELECT MAX(id) FROM $gainers_loosers WHERE symbol='". $disticnt_symbol_result['symbol'] ."')";

	        $query=mysqli_query($conn,$sql);
	        $result = mysqli_fetch_array($query,MYSQLI_ASSOC);

		    $data[$counter]['symbol'] = $result['symbol'];
		    $data[$counter]['type'] = $result['type'];
		    $data[$counter]['rank'] = $result['rank'];
		    $data[$counter]['executed'] = $result['executed'];
		    $data[$counter]['lot_size'] = $result['lot_size'];
		    $data[$counter]['normal_margin'] = $result['normal_margin'];
		    $data[$counter]['mis_margin'] = $result['mis_margin'];

	        $counter++;
    }

	/*$sql = "SELECT MAX($gainers_loosers.id),
                   $gainers_loosers.symbol, 
                   $gainers_loosers.type, 
                   $gainers_loosers.rank, 
                   $gainers_loosers.executed,
                   $fo_margin.lot_size,
                   $fo_margin.normal_margin,
                   $fo_margin.mis_margin
                   
            FROM $gainers_loosers 
            INNER JOIN $fo_margin ON $fo_margin.symbol = $gainers_loosers.symbol
            GROUP BY symbol ORDER BY executed DESC, rank DESC";

	 $query=mysqli_query($conn,$sql);
	 $counter = 0;
	 while ( $result = mysqli_fetch_array($query,MYSQLI_ASSOC) ) {
		 $data[$counter]['symbol'] = $result['symbol'];
		 $data[$counter]['type'] = $result['type'];
		 $data[$counter]['rank'] = $result['rank'];
		 $data[$counter]['executed'] = $result['executed'];
		 $data[$counter]['lot_size'] = $result['lot_size'];
		 $data[$counter]['normal_margin'] = $result['normal_margin'];
		 $data[$counter]['mis_margin'] = $result['mis_margin'];

		 $counter++;
	 }*/
}
?>
<html>
<head>
	<title>Testing</title>
	<!--<META HTTP-EQUIV="refresh" CONTENT="2">-->
</head>
<body>
</body>
<div style="float: left;width: 650px;">
	<h3>F&O Gainers</h3>
	<table border="1">
		<tr>
			<th>Symbol</th>
			<th>Type</th>
			<th>Rank</th>
			<th>Lot</th>
			<th>Normal Margin</th>
			<th>MIS Margin</th>
			<th>Executed</th>
		</tr>
		<?php
		if ( isset( $data ) && !empty( $data ) ) {
			foreach ( $data as $key => $value ) {
				if ( $value['type'] == 'fnoGainers' ) {
					?>
					<tr>
						<td><?php print $value['symbol']; ?></td>
						<td><?php print $value['type']; ?></td>
						<td><?php print $value['rank']; ?></td>
						<td><?php print $value['lot_size']; ?></td>
						<td><?php print $value['normal_margin']; ?></td>
						<td><?php print $value['mis_margin']; ?></td>
						<td><?php print $value['executed']; ?></td>
					</tr>
					<?php
				}
			}
		}
		?>
	</table>
</div>
<div style="float: left;">
	<h3>F&O Loosers</h3>
	<table border="1">
		<tr>
			<th>Symbol</th>
			<th>Type</th>
			<th>Rank</th>
			<th>Lot</th>
			<th>Normal Margin</th>
			<th>MIS Margin</th>
			<th>Executed</th>
		</tr>
		<?php
		if ( isset( $data ) && !empty( $data ) ) {
			foreach ( $data as $key => $value ) {
				if ( $value['type'] == 'fnoLosers' ) {
					?>
					<tr>
						<td><?php print $value['symbol']; ?></td>
						<td><?php print $value['type']; ?></td>
						<td><?php print $value['rank']; ?></td>
						<td><?php print $value['lot_size']; ?></td>
						<td><?php print $value['normal_margin']; ?></td>
						<td><?php print $value['mis_margin']; ?></td>
						<td><?php print $value['executed']; ?></td>
					</tr>
					<?php
				}
			}
		}
		?>
	</table>
</div>
</html>
