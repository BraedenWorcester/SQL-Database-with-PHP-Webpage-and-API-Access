<?php
$un = 'webuser';
$pw = 'somepassword'; # password redacted for obvious reasons
$db = 'equipment';
$host = 'localhost';
$dblink = new mysqli($host, $un, $pw, $db);
$dir = "/var/www/html/device_files/";

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
	$file = $dir . $_POST["device_id"] . "/" . $_POST['download_file'];
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.basename($file));
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file));
	ob_clean();
	flush();
	readfile($file);
	exit;
}

if($_POST['delete_file'] != ""){
	$sql = "delete from device_file_paths where file_path = '".$dir.$_POST["device_id"]."/".trim(preg_replace("/\r|\n/", "", $_POST["delete_file"]))."'";
	if(!$result = $dblink->query($sql)){
		header('Content-Type: application/json');
		header('HTTP/1.1 500 internal server error');
		$output[]="error: SQL query '" . $sql . "' failed to execute";
		$response=json_encode($output);
		echo $response;
		die();
	}
	if (!unlink($dir.$_POST["device_id"]."/".trim(preg_replace("/\r|\n/", "", $_POST["delete_file"])))){
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]="error: failed to delete";
		$response=json_encode($output);
		echo $response;
		die();
	}
}

foreach ($_FILES as $file){
		$dir = $dir . $_POST["device_id"]."/";
		if (!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}
		$device_file = $dir."".basename($file["name"]);
		$device_file_exension = strtolower(pathinfo($device_file,PATHINFO_EXTENSION));

		if($device_file_exension != "pdf"){
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]="error: file must be pdf";
			$response=json_encode($output);
			echo $response;
			die();
		}
		$count = 0;
		while(file_exists($device_file)){
			$count += 1;
			if ($count == 10){
				header('Content-Type: application/json');
				header('HTTP/1.1 200 OK');
				$output[]="error: too many duplicate files";
				$response=json_encode($output);
				echo $response;
				die();
			}
			$device_file = $dir.basename($file["name"], '.pdf').'-'.$count.'.pdf';
		}
		if (move_uploaded_file($file["tmp_name"], $device_file)) {
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]="success: file uploaded";
			$response=json_encode($output);
			echo $response;
		} 
		else {
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]="error: failed uploading";
			$response=json_encode($output);
			echo $response;
			die();
		}
		$sql = "insert into device_file_paths (file_path, device_id)
				values ('".$device_file."', "."'".$_POST["device_id"]."')";
		
		if (!$result = $dblink->query($sql)){
			header('Content-Type: application/json');
			header('HTTP/1.1 500 internal server error');
			$output[]="error: SQL query '" . $sql . "' failed to execute";
			$response=json_encode($output);
			echo $response;
		}
		
}


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
