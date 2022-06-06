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

$sql = "select ";
if ($_POST["retSerial_number"] == "yes"){$sql = $sql."serial_number,";}
if ($_POST["retStatus"] == "yes"){$sql = $sql."status,";}
if ($_POST["retBrand"] == "yes"){$sql = $sql."brand,";}
if ($_POST["retType"] == "yes"){$sql = $sql."type,";}
$sql = rtrim($sql, ',');

$sql = $sql." from devices where ";

if ($_POST["query1"] == $_POST["query2"] && $_POST["val1"] != $_POST["val2"]){
	die("Invalid Query: '".$_POST["query1"]."' cannot be '".$_POST["val1"]."' and '".$_POST["val2"]."'");
}
elseif($_POST["query1"] == $_POST["query2"] && $_POST["query2"] == ""){
	die("Invalid Query: Must select a query condition; I'm not gonna print the entire database because I'm pretty sure Amazon will claim my house");
}
elseif ($_POST["query1"] == $_POST["query2"] || $_POST["query2"] == ""){
	if ($_POST["val1"] == ""){
		die("Invalid Query: '".$_POST["query1"]."' cannot be blank");
	}
	if(preg_match('#^[A-Z0-9 ]+$#i',$_POST["val1"]) == true){
		if ($_POST['exactsearch'] == "yes") {
			$sql = $sql.$_POST["query1"]." = '".$_POST["val1"]."'";
		}
		else {
			$sql = $sql.$_POST["query1"]." like '%".$_POST["val1"]."%'";
		}
	}
	else{
		die("Invalid Query: Invalid characters");
	}
}
elseif ($_POST["query1"] == ""){
	if ($_POST["val2"] == ""){
		die("Invalid Query: '".$_POST["query2"]."' cannot be blank");
	}
	if(preg_match('#^[A-Z0-9 ]+$#i',$_POST["val2"]) == true){
		if ($_POST['exactsearch'] == "yes") {
			$sql = $sql.$_POST["query2"]." = '".$_POST["val2"]."'";
		}
		else {
			$sql = $sql.$_POST["query2"]." like '%".$_POST["val2"]."%'";
		}
	}
	else{
		die("Invalid Query: Invalid characters");
	}
}
else{
	if ($_POST["val1"] == ""){
		die("Invalid Query: '".$_POST["query1"]."' cannot be blank");
	}
	if ($_POST["val2"] == ""){
		die("Invalid Query: '".$_POST["query2"]."' cannot be blank");
	}
	if(preg_match('#^[A-Z0-9 ]+$#i',$_POST["val1"]) == true && preg_match('#^[A-Z0-9 ]+$#i',$_POST["val2"]) == true){
		if ($_POST['exactsearch'] == "yes") {
			$sql = $sql.$_POST["query1"]." = '".$_POST["val1"]."' and ".$_POST["query2"]." = '".$_POST["val2"]."'";
		}
		else {
			$sql = $sql.$_POST["query1"]." like '%".$_POST["val1"]."%' and ".$_POST["query2"]." like '%".$_POST["val2"]."%'";
		}
	}
	else{
		die("Invalid Query: Invalid characters");
	}
}


if ($sql != "select "){
	$result = $dblink->query($sql) or 
		die("Uh oh, $sql failed!");
	
	echo "<p>".$result->num_rows." devices found</p>";
	while ($data = $result->fetch_array(MYSQLI_ASSOC))
	{
		echo "<p>";
		if ($_POST["retSerial_number"] == "yes"){
			echo "Serial Number: '$data[serial_number]'<br>";
		}
		if ($_POST["retStatus"] == "yes"){
			echo "Status: '$data[status]'<br>";
		}
		if ($_POST["retBrand"] == "yes"){
			echo "Brand: '$data[brand]'<br>";
		}
		if ($_POST["retType"] == "yes"){
			echo "Type: '$data[type]'<br>";
		}
		
		echo "</p>";
	}

}
?>