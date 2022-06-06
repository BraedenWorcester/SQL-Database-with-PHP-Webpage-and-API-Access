 <form action="query.php" method="post">
	Search Devices<br>
	------------------------------------------------------------------------------------------------------------------------<br>
	<label for="queryBy">Query by:</label><br>
	<select name="query1" id="query1">
		<option value="">--- Nothing ---</option>
		<option value="brand">Brand</option>
		<option value="type">Type</option>
		<option value="serial_number">Serial Number</option>
	</select><br>
	<select name="query2" id="query2">
		<option value="">--- Nothing ---</option>
		<option value="brand">Brand</option>
		<option value="type">Type</option>
		<option value="serial_number">Serial Number</option>
	</select><br><br>
	<label for="query1Val">Query input:</label><br>
		<input type="text" id="val1" name="val1"><br>
		<input type="text" id="val2" name="val2"><br>
	<label for="exact">Exact Search (faster but no partial matches)</label>
			<input type="checkbox" name="exactsearch" value="yes"/><br><br>
	<label for="queryFor">Query output:</label><br>
		<label for="queryForSerial_number">Serial Number</label>
			<input type="checkbox" name="retSerial_number" value="yes" checked/><br>
		<label for="queryForStatus">Status</label>
			<input type="checkbox" name="retStatus" value="yes" checked/><br>
		<label for="queryForBrand">Brand</label>
			<input type="checkbox" name="retBrand" value="yes" checked/><br>
		<label for="queryForType">Type</label>
			<input type="checkbox" name="retType" value="yes" checked/><br>
	<br>
	<input type="submit" /><br>
	------------------------------------------------------------------------------------------------------------------------<br>
</form>
<br><br>
<form action="addDevice.php" method="post">
	Add Device<br>
	------------------------------------------------------------------------------------------------------------------------<br>
	<label for="serial">Device Serial Number: </label><br>
		<input type="text" id="serial_number" name="serial_number"><br>
	<label for="bran">Device Brand: </label><br>
		<input type="text" id="brand" name="brand"><br>
	<label for="typ">Device Type: </label><br>
		<input type="text" id="type" name="type"><br><br>
	<input type="submit"/><br>
	------------------------------------------------------------------------------------------------------------------------<br>
</form>
<br><br>
<form action="modifyDevice.php" method="post">
	Modify Device<br>
	------------------------------------------------------------------------------------------------------------------------<br>
	<label for="serial">Device Serial Number: </label><br>
		<input type="text" id="serial_number" name="serial_number"><br><br>
	<input type="submit"/><br>
	------------------------------------------------------------------------------------------------------------------------<br>
</form>
<br><br>
<form action="deleteDevice.php" method="post">
	Delete Device<br>
	------------------------------------------------------------------------------------------------------------------------<br>
	<label for="serial">Device Serial Number: </label><br>
		<input type="text" id="serial_number" name="serial_number"/><br><br>
	<input type="submit" value = "delete"/><br>
	------------------------------------------------------------------------------------------------------------------------<br>
</form>
<br><br>
<form action="manageFiles.php" method="post">
	Manage Device Files<br>
	------------------------------------------------------------------------------------------------------------------------<br>
	<label for="serial">Device Serial Number: </label><br>
		<input type="text" id="serial_number" name="serial_number"><br><br>
	<input type="submit"/><br>
	------------------------------------------------------------------------------------------------------------------------<br>
</form>