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

if ($_POST["device_id"] != null){
	$sql = $sql . "device_id = '" . $_POST["device_id"] . "' ";
    if ($dblink->query("select device_id from devices where device_id = '" . $_POST["device_id"] . "' ")->num_rows == 0){
        header('Content-Type: application/json');
        header('HTTP/1.1 200 OK');
        $output[]="error: device with device_id '" . $_POST["device_id"] . "' not found";
        $response=json_encode($output);
        echo $response;
        die();
    }
}
else if ($_POST["serial_number"] != null){
	$sql = $sql . "serial_number = '" . $_POST["serial_number"] . "' ";
    if ($dblink->query("select device_id from devices where serial_number = '" . $_POST["serial_number"] . "' ")->num_rows == 0){
        header('Content-Type: application/json');
        header('HTTP/1.1 200 OK');
        $output[]="error: device with serial_number '" . $_POST["serial_number"] . "' not found";
        $response=json_encode($output);
        echo $response;
        die();
    }
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