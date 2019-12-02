<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "daibik";

//print '<pre>';
//print_r($_POST);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error ) {
	die("Connection failed: " . $conn->connect_error);
}
else {
    $sql = "SELECT date_added FROM fo_gainers group by date_added order by date_added DESC";
	$query=mysqli_query($conn,$sql);
	while ( $result = mysqli_fetch_array($query,MYSQLI_ASSOC) ) {
		$data[] = $result;
	}
	$selected = isset( $_POST['date'] )? trim( $_POST['date'] ):'';
}
?>
<html>
<head>
	<title>Gainers</title>
</head>
<body>
<form action="gainers_display.php" method="POST">
	<label>Date</label>
	<select name="date" id="date" onchange="this.form.submit()">
		<option value="">Select</option>
		<?php
		  foreach ($data as $key => $value ) {
		?>
			  <option value="<?php print $value['date_added']; ?>" <?php print ($selected == $value['date_added'])? 'selected': '';?>><?php print date('d/m/Y', strtotime($value['date_added'])); ?> </option>
		<?php
		  }
		?>
	</select>
	<table>
		<tr>
			<th>Symbol</th>
			<th>Percentage High</th>
		</tr>
		<?php
		  $sql_details = "SELECT * FROM fo_gainers WHERE date_added='". $selected ."' ORDER BY high_percentage DESC LIMIT 0,20";
		  $query_details=mysqli_query($conn,$sql_details);
		  while ( $result = mysqli_fetch_array($query_details,MYSQLI_ASSOC) ) {
		?>
			  <tr>
				  <td><?php print $result['symbol']; ?></td>
				  <td><?php print $result['high_percentage']; ?></td>
			  </tr>
		<?php
		   }
		?>
	</table>
</form>
</body>
</html>
