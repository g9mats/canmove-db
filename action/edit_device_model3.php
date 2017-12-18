<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$device_model_id=$_POST['device_model_id'];
if ($device_model_id=="") {
	echo "<p>You must specify device model id.</p>";
	return;
}
$model=$_POST['model'];
if ($model=="") {
	echo "<p>You must specify device model.</p>";
	return;
}
$manufacturer=$_POST['manufacturer'];
$description=$_POST['description'];
$weight=$_POST['weight'];
if ($weight == "") $weight = null;
require_once $DBRoot."/lib/DBLink.php";

// SQL: update device model record
$sql="
update r_device_model set
	model = $2,
	manufacturer = $3,
	description = $4,
	weight = $5
where device_model_id = $1
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Update device model
if ($res = $db->execute($sql,
		array($device_model_id,$model,$manufacturer,$description,$weight))) {
	echo "<p>Device model updated.</p>";
}

$db->disconnect();
?>
