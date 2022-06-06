<form action="index.php" method="post">
	<label for="typ">Return Home: </label>
	<input type="submit" value="return"><br>
</form>

<?php
$un = 'webuser';
$pw = 'somepassword'; # password redacted for obvious reasons
$db = 'equipment';
$host = 'localhost';
$dblink = new mysqli($host, $un, $pw, $db);

if(isset($_POST['change'])){
	if((preg_match('#^[A-Z0-9 ]+$#i',$_POST['serial_number_new']) == false && $_POST['serial_number_new'] != "") ||
	   (preg_match('#^[A-Z0-9 ]+$#i',$_POST['brand']) == false && $_POST['brand'] != "")||
	   (preg_match('#^[A-Z0-9 ]+$#i',$_POST['type']) == false && $_POST['type'] != "")) {	
		die("Invalid Modification: Invalid characters");
	}
	session_start();
	$sql = "update devices
			set ";
	if ($_POST["serial_number_new"] != "") {
		$sql = $sql."serial_number = '".$_POST["serial_number_new"]."', ";
	}
	else {
		$sql = $sql."serial_number = '".$_SESSION['serial_number']."', ";
	}
	if ($_POST["brand"] != "") {
		$sql = $sql."brand = '".$_POST["brand"]."', ";
	}
	else {
		$sql = $sql."brand = '".$_SESSION["brand"]."', ";
	}
	if ($_POST["type"] != "") {
		$sql = $sql."type = '".$_POST["type"]."', ";
	}
	else {
		$sql = $sql."type = '".$_SESSION["type"]."', ";
	}
	$sql = $sql."status = '".$_POST["status"]."' 
				where device_id = '".$_SESSION["device_id"]."' ";
	if ($dblink->query($sql)){
		echo "Device modified successfully";
	}
	else {
		die("Uh oh, $sql failed! $dblink->error");
	}
	$sql = "select serial_number, brand, type, status, device_id from devices where device_id = '".$_SESSION["device_id"]."'";
	$result = $dblink->query($sql) or 
			die("Uh oh, $sql failed! $dblink->error");
	$data = $result->fetch_array(MYSQLI_ASSOC);
}
else {
	if ($_POST['serial_number'] == "") {
		die("Invalid Device: Device requires 'serial number'");
	}

	if(preg_match('#^[A-Z0-9 ]+$#i',$_POST['serial_number']) == false) {	
		die("Invalid Device: Invalid characters");
	}

	$sql = "select brand, type, status, device_id from devices where serial_number = '".$_POST['serial_number']."'";
	$result = $dblink->query($sql) or 
			die("Uh oh, $sql failed! $dblink->error");
	$data = $result->fetch_array(MYSQLI_ASSOC);
	$data[serial_number] = $_POST['serial_number'];
	if ($result->num_rows == 0){
		die("No devices found of serial number '".$_POST['serial_number']."'");
	}
	session_start();
}
$_SESSION['device_id'] = $data[device_id];
$_SESSION['serial_number'] = $data[serial_number];
$_SESSION['brand'] = $data[brand];
$_SESSION['type'] = $data[type];
$_SESSION['status'] = $data[status];
?>

<br><br>
<form action="modifyDevice.php" method="post">
	Modify Device (leave blank for no change)<br>
	------------------------------------------------------------------------------------------------------------------------<br>
	<label for="serialnew">Device Serial Number: </label> <?php echo "'".$data[serial_number]."'" ?> to 
		<input type="text" id="serial_number_new" name="serial_number_new"><br>
	<label for="bran">Device Brand: </label> <?php echo "'".$data[brand]."'" ?> to 
		<input type="text" id="brand" name="brand"><br>
	<label for="typ">Device Type: </label> <?php echo "'".$data[type]."'" ?> to 
		<input type="text" id="type" name="type"><br>
	<label for="typ">Device Status: </label> <?php echo "'".$data[status]."'" ?> to 
		<select name="status" id="status">
			<?php 
				if ($data[status] == "active"){
					echo "<option value="."active".">active</option> <option value="."inactive".">inactive</option>";
				}
				else {
					echo "<option value="."inactive".">inactive</option> <option value="."active".">active</option>";
				}
			?>
		</select><br><br>
	<input type="submit" <?php if ($result->num_rows != 0){echo "name = \"change\"";}?>/><br>
	------------------------------------------------------------------------------------------------------------------------<br>
</form>