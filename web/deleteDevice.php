<form action="index.php" method="post">
	<label for="typ">Return Home: </label>
	<input type="submit" value="return"><br>
</form>

<?php
$un = 'webuser';
$pw = 'redacted';
$db = 'equipment';
$host = 'localhost';
$dblink = new mysqli($host, $un, $pw, $db);

foreach($_POST as $key => $value) {
    $_POST[$key] = $dblink->real_escape_string($value);
}

if(isset($_POST['deleting'])){
	session_start();
	$sql = "delete from device_file_paths where device_id = '".$_SESSION["device_id"]."'";
	$result = $dblink->query($sql) or 
		die("Uh oh, $sql failed! $dblink->error");
	$sql = "delete from devices where device_id = '".$_SESSION["device_id"]."'";
	$result = $dblink->query($sql) or 
		die("Uh oh, $sql failed! $dblink->error");
	die("device deleted :(");
}

if ($_POST["serial_number"] == "") {
	die("Invalid Device: Device requires 'serial number'");
}

$sql = "select device_id, type, brand from devices where serial_number = '".$_POST["serial_number"]."'";
$result = $dblink->query($sql) or 
		die("Uh oh, $sql failed! $dblink->error");
if ($result->num_rows == 0){
		die("No devices found of serial number '".$_POST['serial_number']."'");
	}
$data = $result->fetch_array(MYSQLI_ASSOC);
$data[serial_number] = $_POST['serial_number'];
session_start();
$_SESSION["device_id"] = $data[device_id];
?>

<br><br>
<form action="deleteDevice.php" method="post">
	Are you sure you want to delete this device?<br>
	------------------------------------------------------------------------------------------------------------------------<br>
	<label for="serial">Device Serial Number: </label> <?php echo "'".$data[serial_number]."'" ?><br>
	<label for="bran">Device Brand: </label> <?php echo "'".$data[brand]."'" ?><br>
	<label for="typ">Device Type: </label> <?php echo "'".$data[type]."'" ?><br><br>
	<input type="submit" value = "confirm delete" name = "deleting"/><br>
	------------------------------------------------------------------------------------------------------------------------<br>
</form>