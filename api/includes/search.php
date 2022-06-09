<?php
$un = 'webuser';
$pw = 'redacted'; # redacted for obvious reasons
$db = 'equipment';
$host = 'localhost';
$dblink = new mysqli($host, $un, $pw, $db);

foreach($_POST as $key => $value) {
    $_POST[$key] = $dblink->real_escape_string($value);
}

$return_columns = explode(",", "device_id,".$_POST["return_columns"]);

$sql = "SELECT ";
if ($return_columns[1] == ""){
	$sql = $sql . "*";
}
else{
	for ($i = 0; $i < sizeof($return_columns); $i += 1){
		$sql = $sql . $return_columns[$i] . ",";
	}
}
$sql = rtrim($sql, ',') . " FROM devices WHERE ";

if ($_POST["serial_number"] != ""){
	if($_POST["exact"] == "true")
		$sql = $sql . "serial_number = '" . $_POST["serial_number"] . "' and ";
	else
		$sql = $sql . "serial_number like '%" . $_POST["serial_number"] . "%' and ";
}
if ($_POST["brand"] != ""){
	if($_POST["exact"] == "true")
		$sql = $sql . "brand = '" . $_POST["brand"] . "' and ";
	else
		$sql = $sql . "brand like '%" . $_POST["brand"] . "%' and ";
}
if ($_POST["type"] != ""){
	if($_POST["exact"] == "true")
		$sql = $sql . "type = '" . $_POST["type"] . "' and ";
	else
		$sql = $sql . "type like '%" . $_POST["type"] . "%' and ";
}
if ($_POST["status"] == "active" || $_POST["status"] == "inactive"){
	$sql = $sql . "status = '" . $_POST["status"] . "' and ";
}

$sql = substr($sql, 0, -5);

if ($result = $dblink->query($sql) ){
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$response["num_rows"] = $result->num_rows;
	while ($data = $result->fetch_array(MYSQLI_ASSOC)){
		$device = array();
		if ($return_columns[1] != "*" && $return_columns[1] != "") {
			for ($i = 0; $i < sizeof($return_columns); $i += 1){
				$device[$return_columns[$i]] = $data[$return_columns[$i]];
			}
			unset($device["device_id"]);
		}
		else {
			$device = $data;
			unset($device["device_id"]);
		}
		$response[$data["device_id"]] = $device;
	}
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	echo json_encode($response) . "\n";
}
else {
	header('Content-Type: application/json');
	header('HTTP/1.1 500 internal server error');
	$output[]="error: SQL query '" . $sql . "' failed to execute";
	$response=json_encode($output);
	echo $response;
}

?>