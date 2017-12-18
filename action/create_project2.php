<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$title=$_POST['title'];
if ($title=="") {
	echo "<p>You must specify a project title.</p>";
	return;
}
$owner_id=$_POST['owner_id'];
if ($owner_id=="") {
	echo "<p>You must specify a project owner.</p>";
	return;
}
$remark=$_POST['remark'];
require_once $DBRoot."/lib/DBLink.php";

// SQL: select the next project_id and the current user_id
$sql_ids="
select
	nextval('p_project_project_id_seq') project_id,
	person_id
from r_person
where drupal_id = $1
";

// SQL: insert new project
$sql_project="
insert into p_project (project_id, title, remark)
values ($1, $2, $3)
";

// SQL: insert new project role record
$sql_role="
insert into p_project_role (project_id, user_id, user_role)
values ($1, $2, $3)
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Get id values and create project and role record
if ($resI = $db->query($sql_ids,array($user->uid))) {
	$project_id = $resI[0]['project_id'];
	$user_id = $resI[0]['person_id'];
	$resP = $db->execute($sql_project, array($project_id,$title,$remark));
	$resA = $db->execute($sql_role, array($project_id,$owner_id,"O"));
	if ($user_id <> $owner_id)
		$resA = $db->execute($sql_role, array($project_id,$user_id,"A"));
	echo "<p>New project created.</p>";
}

$db->disconnect();
?>
