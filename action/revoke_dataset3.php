<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
$person_id=$_POST['person_id'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
if ($person_id=="") {
	echo "<p>You must specify a user.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: delete dataset role record
$sql="
delete from  p_dataset_role
where dataset_id = $1
  and user_id = $2
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Delete role
if ($res = $db->execute($sql, array($dataset_id,$person_id,))) {
	echo "<p>User role deleted.</p>";
}

$db->disconnect();
?>
