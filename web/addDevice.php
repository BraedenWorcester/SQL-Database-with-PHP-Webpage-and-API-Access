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

if ($_POST["serial_number"] == "") {
	die("Invalid Device: Device requires 'serial number'");
}
if ($_POST["brand"] == "") {
	die("Invalid Device: Device requires 'brand'");
}
if ($_POST["type"] == "") {
	die("Invalid Device: Device requires 'type'");
}
if(preg_match('#^[A-Z0-9 ]+$#i',$_POST["brand"]) == false || 
   preg_match('#^[A-Z0-9 ]+$#i',$_POST["type"]) == false || 
   preg_match('#^[A-Z0-9 ]+$#i',$_POST["serial_number"]) == false) {	
	die("Invalid Device: Invalid characters");
}

$sql = "insert into devices (serial_number, brand, type, status)
		values ('".$_POST["serial_number"]."', '".$_POST["brand"]."', '".$_POST["type"]."', 'active')";
$result = $dblink->query($sql) or 
		die("Uh oh, $sql failed! $dblink->error");
echo "Device successfully added"

?>