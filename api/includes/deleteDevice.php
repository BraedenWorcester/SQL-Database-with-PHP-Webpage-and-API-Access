<?php
$un = 'webuser';
$pw = 'redacted'; # redacted for obvious reasons
$db = 'equipment';
$host = 'localhost';
$dblink = new mysqli($host, $un, $pw, $db);

$sql = "delete from devices where ";

foreach($_POST as $key => $value) {
    $_POST[$key] = $dblink->real_escape_string($value);
}

if ($_POST["device_id"] != ""){
	$sql = $sql . "device_id = '" . $_POST["device_id"] . "'";
}
else if ($_POST["serial_number"] != ""){
	$sql = $sql . "serial_number = '" . $_POST["serial_number"] . "'";
}
else {
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="error: require input for at least one of: device_id, serial_number";
	$response=json_encode($output);
	echo $response;
	die();
}

if ($result = $dblink->query($sql)){
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="success: device is removed";
	echo json_encode($output);
}
else {
	header('Content-Type: application/json');
	header('HTTP/1.1 500 internal server error');
	$output[]="error: SQL query '" . $sql . "' failed to execute";
	$response=json_encode($output);
	echo $response;
}
?>