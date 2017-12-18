<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$project_id=$_POST['project_id'];
$title=$_POST['title'];
$remark=$_POST['remark'];
if ($project_id=="") {
	echo "<p>You must specify a project.</p>";
	return;
}
if ($title=="") {
	echo "<p>You must specify a project title.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: update project
$sql="
update p_project set
	title = $2,
	remark = $3
where project_id = $1
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Update project
if ($res = $db->execute($sql,array($project_id,$title,$remark))) {
	echo "<p>Project updated.</p>";
}

$db->disconnect();
?>
