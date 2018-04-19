<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$person_id=$_POST['person_id'];
$time_zone=$_POST['time_zone'];
if ($person_id=="") {
	echo "<p>You must specify a person.</p>";
	return;
}
if ($time_zone=="") {
	echo "<p>You must specify a time zone.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: update person record
$sql="
update r_person set
	time_zone = $2
where person_id = $1
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Update settings
if ($res = $db->execute($sql,
		array($person_id,$time_zone))) {
	echo "<p>Settings updated.</p>";
}

$db->disconnect();
?>
