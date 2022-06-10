<?php
$un = 'webuser';
$pw = 'redacted'; # redacted for obvious reasons
$db = 'equipment';
$host = 'localhost';
$dblink = new mysqli($host, $un, $pw, $db);
$root_dir = "/var/www/html/device_files/";

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

if ($_POST["device_id"] == "")
{
	if ($_POST["serial_number"] != "")
	{
		$sql = "select device_id from devices where serial_number = '" . $_POST["serial_number"] . "'";
		if ($result = $dblink->query($sql))
		{
			$_POST["device_id"] = $result->fetch_array(MYSQLI_ASSOC)["device_id"];
		}
		else 
		{
			header('Content-Type: application/json');
			header('HTTP/1.1 500 internal server error');
			$output[]="error: SQL query '" . $sql . "' failed to execute";
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
}

if($_POST['download_file'] != ""){
    $zip = new ZipArchive;
    $zip_name = $root_dir . "tmp/" . time() . ".zip";
    $zip_base_name = basename($zip_name, '.zip');
    $count = 0;
    while(file_exists($zip_name)){
        $count += 1;
        $zip_name = $root_dir.$zip_base_name.'-'.$count.'.zip';
    }
    $zip->open($zip_name, ZipArchive::CREATE|ZipArchive::OVERWRITE);
    $files = explode(",", $_POST['download_file']);
    foreach ($files as $file){
        $file = $root_dir . $_POST["device_id"] . "/" . $file;
        if (file_exists($file)) {
            $zip->addFile($file, basename($file));
        }
    }
    $zip->close($zip_name);
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($zip_name));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($zip_name));
    ob_clean();
    flush();
    readfile($zip_name);
    exit;
}

if($_POST['delete_file'] != ""){
    $files = explode(",", $_POST['delete_file']);
    foreach ($files as $file){
        $sql = "delete from device_file_paths where file_path = '".$root_dir.$_POST["device_id"]."/".trim(preg_replace("/\r|\n/", "", $file))."'";
        if(!$result = $dblink->query($sql)){
            header('Content-Type: application/json');
            header('HTTP/1.1 500 internal server error');
            $output[]="failed delete: " . $file . " (SQL failure)";
            $response=json_encode($output);
        }
        else if (!unlink($root_dir.$_POST["device_id"]."/".trim(preg_replace("/\r|\n/", "", $file)))){
            header('Content-Type: application/json');
            header('HTTP/1.1 200 OK');
            $output[]="possible failed delete: " . $file . " (failed to unlink - file may not exist)";
            $response=json_encode($output);
        }
        else {
            header('Content-Type: application/json');
            header('HTTP/1.1 200 OK');
            $output[]="successful delete: " . $file;
            $response=json_encode($output);
        }
    }
}

foreach ($_FILES as $file){
    
    $dir = $root_dir . $_POST["device_id"]."/";
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    $device_file = $dir."".basename($file["name"]);
    $device_file_exension = strtolower(pathinfo($device_file,PATHINFO_EXTENSION));

    if($device_file_exension != "pdf"){
        header('Content-Type: application/json');
        header('HTTP/1.1 200 OK');
        $output[]="failed upload: " . $file["name"] . " (file is not a PDF)";
        $response=json_encode($output);
        continue;
    }
    $count = 0;
    while(file_exists($device_file)){
        $count += 1;
        $device_file = $dir.basename($file["name"], '.pdf').'-'.$count.'.pdf';
    }
    if (move_uploaded_file($file["tmp_name"], $device_file)) {
        header('Content-Type: application/json');
        header('HTTP/1.1 200 OK');
        $output[]="successful upload: " . $file["name"];
        $response=json_encode($output);
    } 
    else {
        header('Content-Type: application/json');
        header('HTTP/1.1 200 OK');
        $output[]="failed upload: " . $file["name"] . " (reason unknown)";
        $response=json_encode($output);
    }
    $sql = "insert into device_file_paths (file_path, device_id)
            values ('".$device_file."', "."'".$_POST["device_id"]."')";
    
    if (!$result = $dblink->query($sql)){
        header('Content-Type: application/json');
        header('HTTP/1.1 500 internal server error');
        $output[]="error: SQL query '" . $sql . "' failed to execute";
        $response=json_encode($output);
    }
		
}
echo $response . "\n";

$response = null;
$sql = "select file_id, file_path from device_file_paths where device_id = '" . $_POST["device_id"] . "'";
if ($result = $dblink->query($sql)){
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$response["num_rows"] = $result->num_rows;
	while ($data = $result->fetch_array(MYSQLI_ASSOC)){
		$response[$data["file_id"]] = $data["file_path"];
	}
	echo json_encode($response);
}
else {
	header('Content-Type: application/json');
	header('HTTP/1.1 500 internal server error');
	$output[]="error: SQL query '" . $sql . "' failed to execute";
	$response=json_encode($output);
	echo $response;
}
?>
