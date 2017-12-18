<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$model=$_POST['model'];
if ($model=="") {
	echo "<p>You must specify model.</p>";
	return;
}
$manufacturer=$_POST['manufacturer'];
$weight=$_POST['weight'];
if ($weight=="") {
	$weight=NULL;
}
$description=$_POST['description'];
require_once $DBRoot."/lib/DBLink.php";

// SQL: insert device model record
$sql="
insert into r_device_model (model, manufacturer, description, weight)
	values ($1, $2, $3, $4)
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Insert device_model
if ($res = $db->execute($sql, array($model,$manufacturer,$description,$weight))) {
	echo "<p>Device model created.</p>";
}

$db->disconnect();
?>
