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



$sql = "select ";
if ($_POST["retSerial_number"] == "yes"){$sql = $sql."serial_number,";}
if ($_POST["retStatus"] == "yes"){$sql = $sql."status,";}
if ($_POST["retBrand"] == "yes"){$sql = $sql."brand,";}
if ($_POST["retType"] == "yes"){$sql = $sql."type,";}
$sql = rtrim($sql, ',');

$sql = $sql." from devices where ";

$query1 = $dblink->real_escape_string($_POST["query1"]);
$query2 = $dblink->real_escape_string($_POST["query2"]);
$val1 = $dblink->real_escape_string($_POST["val1"]);
$val2 = $dblink->real_escape_string($_POST["val2"]);

if ($query1 == $query2 && $val1 != $val2){
	die("Invalid Query: '".$query1."' cannot be '".$val1."' and '".$val2."'");
}
elseif($query1 == $query2 && $query2 == ""){
	die("Invalid Query: Must select a query condition; I'm not gonna print the entire database because I'm pretty sure Amazon will claim my house");
}
elseif ($query1 == $query2 || $query2 == ""){
	if ($val1 == ""){
		die("Invalid Query: '".$query1."' cannot be blank");
	}
    if ($_POST['exactsearch'] == "yes") {
        $sql = $sql.$query1." = '".$val1."'";
    }
    else {
        $sql = $sql.$query1." like '%".$val1."%'";
    }
}
elseif ($query1 == ""){
	if ($val2 == ""){
		die("Invalid Query: '".$query2."' cannot be blank");
	}
    if ($_POST['exactsearch'] == "yes") {
        $sql = $sql.$query2." = '".$val2."'";
    }
    else {
        $sql = $sql.$query2." like '%".$val2."%'";
    }
}
else{
	if ($val1 == ""){
		die("Invalid Query: '".$query1."' cannot be blank");
	}
	if ($val2 == ""){
		die("Invalid Query: '".$query2."' cannot be blank");
	}
    if ($_POST['exactsearch'] == "yes") {
        $sql = $sql.$query1." = '".$val1."' and ".$query2." = '".$val2."'";
    }
    else {
        $sql = $sql.$query1." like '%".$val1."%' and ".$query2." like '%".$val2."%'";
    }
}


if ($sql != "select "){
	$result = $dblink->query($sql) or 
		die("Uh oh, $sql failed!");
	
	echo "<p>".$result->num_rows." devices found" . "</p>";
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