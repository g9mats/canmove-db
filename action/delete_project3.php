<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$project_id=$_POST['project_id'];
$answer=$_POST['answer'];
if ($project_id=="") {
	echo "<p>You must specify a project.</p>";
	return;
}
if ($answer=="") {
	echo "<p>You must specify an answer.</p>";
	return;
}
if ($answer=="N") {
	echo "<p>Project not deleted.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: select datasets
$sql_dataset="
select count(*) as dnum
from p_dataset
where project_id = $1
";

// SQL: delete roles
$sql_role="
delete from p_project_role
where project_id = $1
";

// SQL: delete project
$sql_project="
delete from p_project
where project_id = $1
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$res = $db->query($sql_dataset,array($project_id));
if ($res[0]['dnum']>0) {
	echo "<p>You can not delete project while there are still datasets left.</p>";
} else {
	$res = $db->execute($sql_role,array($project_id));
	$res = $db->execute($sql_project,array($project_id));
	echo "<p>Project deleted.</p>";
}

$db->disconnect();
?>
