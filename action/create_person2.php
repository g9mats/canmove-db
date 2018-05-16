<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$first_name=$_POST['first_name'];
$last_name=$_POST['last_name'];
$drupal_id=$_POST['drupal_id'];
$time_zone=$_POST['time_zone'];
if ($first_name=="") {
	echo "<p>You must specify first name.</p>";
	return;
}
if ($last_name=="") {
	echo "<p>You must specify last name.</p>";
	return;
}
if ($drupal_id == "") {
	$drupal_id = null;
}
if ($time_zone=="") {
	echo "<p>You must specify a time zone.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: insert person record
$sql="
insert into r_person (first_name, last_name, drupal_id, time_zone)
	values ($1, $2, $3, $4)
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Insert person
if ($res = $db->execute($sql,
					array($first_name,$last_name,$drupal_id,$time_zone))) {
	echo "<p>Person created.</p>";
}

$db->disconnect();
?>
