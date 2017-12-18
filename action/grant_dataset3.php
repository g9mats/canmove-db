<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
$person_id=$_POST['person_id'];
$user_role=$_POST['user_role'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
if ($person_id=="") {
	echo "<p>You must specify a user.</p>";
	return;
}
if ($user_role=="") {
	echo "<p>You must specify user role.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: check for existing role
$sql_old_role="
select
	count(*) role_entry
from p_dataset_role
where dataset_id = $1
  and user_id = $2
";

// SQL: update user role record
$sql_update_role="
update p_dataset_role
	set user_role = $3
where dataset_id = $1
  and user_id = $2
";

// SQL: insert user role record
$sql_insert_role="
insert into p_dataset_role (dataset_id, user_id, user_role)
values ($1, $2, $3)
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Check for existing role
if ($res = $db->query($sql_old_role, array($dataset_id,$person_id))) {
	if ($res[0]['role_entry']==1) {
		if ($res = $db->execute($sql_update_role,
			array($dataset_id,$person_id,$user_role))) {
			echo "<p>User role updated.</p>";
		}
	} else {
		if ($res = $db->execute($sql_insert_role,
			array($dataset_id,$person_id,$user_role))) {
			echo "<p>User role inserted.</p>";
		}
	}
}

$db->disconnect();
?>
