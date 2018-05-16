<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$person_id=$_POST['person_id'];
$first_name=$_POST['first_name'];
$last_name=$_POST['last_name'];
$drupal_id=$_POST['drupal_id'];
$time_zone=$_POST['time_zone'];
if ($person_id=="") {
	echo "<p>You must specify a person.</p>";
	return;
}
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

// SQL: update person record
$sql="
update r_person set
	first_name = $2,
	last_name = $3,
	drupal_id = $4,
	time_zone = $5
where person_id = $1
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Update person
if ($res = $db->execute($sql,
		array($person_id,$first_name,$last_name,$drupal_id,$time_zone))) {
	echo "<p>Person updated.</p>";
}

$db->disconnect();
?>
