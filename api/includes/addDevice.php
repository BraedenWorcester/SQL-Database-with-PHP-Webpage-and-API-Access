<?php
$un = 'webuser';
$pw = 'somepassword'; # password redacted for obvious reasons
$db = 'equipment';
$host = 'localhost';
$dblink = new mysqli($host, $un, $pw, $db);

if ($_POST["serial_number"] == "") {
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="error: require input for serial_number";
	$response=json_encode($output);
	echo $response;
	die();
}
if ($_POST["brand"] == "") {
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="error: require input for brand";
	$response=json_encode($output);
	echo $response;
	die();
}
if ($_POST["type"] == "") {
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="error: require input for type";
	$response=json_encode($output);
	echo $response;
	die();
}
$defaulted_status = false;
if ($_POST["status"] == "") {
	$_POST["status"] = "inactive";
	$defaulted_status = true;
}

$sql = "insert into devices (serial_number, brand, type, status) values ('" . $_POST["serial_number"] . "', '" . $_POST["brand"] . "', '" . $_POST["type"] . "', '" . $_POST["status"] . "')";

if ($result = $dblink->query($sql)){
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="success: device added";
	if ($defaulted_status){
		$output[]="warning: status defaulted to inactive";
	}
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