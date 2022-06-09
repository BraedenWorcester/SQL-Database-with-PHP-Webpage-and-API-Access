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
$dir = "/var/www/html/device_files/";
session_start();

foreach($_POST as $key => $value) {
    $_POST[$key] = $dblink->real_escape_string($value);
}

if (isset($_POST['downloading'])){
	if($_POST['downloadfile'] != ""){
		$file = $dir.$_SESSION["device_id"]."/".$_POST['downloadfile'];
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
	}
}

elseif (isset($_POST['deleting'])){
	$sql = "delete from device_file_paths where file_path = '".$dir.$_SESSION["device_id"]."/".trim(preg_replace("/\r|\n/", "", $_POST["deleting_file"]))."'";
	$result = $dblink->query($sql) or 
		die("Uh oh, $sql failed! $dblink->error");
	if (!unlink($dir.$_SESSION["device_id"]."/".trim(preg_replace("/\r|\n/", "", $_POST["deleting_file"])))){
		die("jesus deleting files is difficult");
	}
	echo "'".$_POST["deleting_file"]."' successfully deleted, are you proud of yourself?<br><br>";
	die();
}

elseif (isset($_POST['uploading'])){
	$dir = $dir.$_SESSION["device_id"]."/";
	if (!file_exists($dir)) {
    	mkdir($dir, 0777, true);
	}
	$device_file = $dir."".basename($_FILES["device_file"]["name"]);
	$device_file_exension = strtolower(pathinfo($device_file,PATHINFO_EXTENSION));
	
	if($device_file_exension != "pdf"){
		die("file must be of type '.pdf'");
	}
	$count = 0;
	while(file_exists($device_file)){
		$count += 1;
		if ($count == 10){
			die("too many files of the same name, aborting upload");
		}
		$device_file = $dir.basename($_FILES["device_file"]["name"], '.pdf').'-'.$count.'.pdf';
	}
	if (move_uploaded_file($_FILES["device_file"]["tmp_name"], $device_file)) {
		echo "Uploaded '".basename($_FILES["device_file"]["name"])."'";
		if ($count > 0) {
			echo " as '".basename($device_file)."' due to identically named files<br>";
		}
		else {
			echo "<br>";
		}
		echo "<br>";
  	} 
	else {
    	die("failed to upload file");
  	}
	$sql = "insert into device_file_paths (file_path, device_id)
			values ('".$device_file."', "."'".$_SESSION["device_id"]."')";
	$result = $dblink->query($sql) or 
		die("Uh oh, $sql failed! $dblink->error");
	die();
}


if ($_POST["serial_number"] == "" && !isset($_POST['uploading']) && !isset($_POST['deleting']) && !isset($_POST['downloading'])) {
	die("Invalid Device: Device requires 'serial number'");
}
$sql = "select device_id from devices where serial_number = '".$_POST['serial_number']."'";
$result = $dblink->query($sql) or 
		die("Uh oh, $sql failed! $dblink->error");
if ($result->num_rows == 0){
		die("No devices found of serial number '".$_POST['serial_number']."'");
	}
$data = $result->fetch_array(MYSQLI_ASSOC);
$data[serial_number] = $_POST['serial_number'];

$_SESSION["device_id"] = $data[device_id];

$sql = "select file_id, file_path from device_file_paths where device_id = '".$data[device_id]."'";
$result = $dblink->query($sql) or 
		die("Uh oh, $sql failed! $dblink->error");

echo $result->num_rows . " files are associated with device serial number '" . $data[serial_number] . "'<br><br>";
$files = [];
echo "------------------------------------------<br>";
while ($data = $result->fetch_array(MYSQLI_ASSOC)){
	$path = $data[file_path];
	echo basename($path)."<br>";	
	array_push($files, basename($path));
}
echo "------------------------------------------<br><br>";
$vardroplimit = 0;
?>
<br><br>
Upload PDF and Bind to Device<br>
------------------------------------------------------------------------------------------------------------------------<br>
<form action="manageFiles.php" method="post" enctype="multipart/form-data" >
	<input type="file" id="device_file" name="device_file" accept=".pdf"/><br><br>
  	<input type="submit" name="uploading" value="upload"/><br>
------------------------------------------------------------------------------------------------------------------------<br>
</form>
<br><br>
Download File<br>
------------------------------------------------------------------------------------------------------------------------<br>
<form action="manageFiles.php" method="post">
		<label for="down">File to download: </label>
			<select name="downloadfile" id="downloadfile">
				<?php
				for ($i = 0; $i < count($files); $i++){
					echo "<option value=\"".$files[$i]."\">".$files[$i]."</option>\n";
				}
				?>
			</select><br><br>
		<input type="submit" name="downloading" value = "download"/><br>
------------------------------------------------------------------------------------------------------------------------<br>
</form>
<br><br>
Delete File<br>
------------------------------------------------------------------------------------------------------------------------<br>
<form action="manageFiles.php" method="post">
		<label for="delet">File to delete: </label>
			<select name="deleting_file" id="deleting_file">
				<?php
				for ($i = 0; $i < count($files); $i++){
					echo "<option value=\"".$files[$i]."\">".$files[$i]."</option>\n";
				}
				?>
			</select><br><br>
		<input type="submit" name="deleting" value = "delete"/><br>
------------------------------------------------------------------------------------------------------------------------<br>
</form>
