<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$project_id=$_POST['project_id'];
$person_id=$_POST['person_id'];
if ($project_id=="") {
	echo "<p>You must specify a project.</p>";
	return;
}
if ($person_id=="") {
	echo "<p>You must specify a user.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: delete project role record
$sql_project="
delete from  p_project_role
where project_id = $1
  and user_id = $2
";

// SQL: delete any dataset role records
$sql_dataset="
delete from  p_dataset_role
where dataset_id in (
	select dataset_id from p_dataset
	where project_id = $1
	)
  and user_id = $2
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Delete role
if ($res = $db->execute($sql_dataset, array($project_id,$person_id,))) {
	if ($res = $db->execute($sql_project, array($project_id,$person_id,))) {
		echo "<p>User role deleted.</p>";
	}
}

$db->disconnect();
?>
