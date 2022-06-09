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

if ($_POST["serial_number"] == "") {
	die("Invalid Device: Device requires 'serial number'");
}
if ($_POST["brand"] == "") {
	die("Invalid Device: Device requires 'brand'");
}
if ($_POST["type"] == "") {
	die("Invalid Device: Device requires 'type'");
}

$brand = $dblink->real_escape_string($_POST["brand"]);
$type = $dblink->real_escape_string($_POST["type"]);
$serial_number = $dblink->real_escape_string($_POST["serial_number"]);

$sql = "insert into devices (serial_number, brand, type, status)
		values ('".$serial_number."', '".$brand."', '".$type."', 'active')";
$result = $dblink->query($sql) or 
		die("Uh oh, $sql failed! $dblink->error");
echo "Device successfully added"

?>