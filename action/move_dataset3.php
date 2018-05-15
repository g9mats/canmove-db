<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
$project_id=$_POST['project_id'];
if ($project_id=="") {
	echo "<p>You must specify a project.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: select missing role grants
$sql_role="
select
	r.first_name,
	r.last_name,
	case (a.user_role)
		when 'O' then 'Owner'
		when 'A' then 'Admin'
		when 'W' then 'Read'
		when 'R' then 'Read'
	end as user_role
from p_dataset d, p_dataset_role a, r_person r
where d.dataset_id = a.dataset_id
  and a.user_id = r.person_id
  and d.dataset_id = $1
except
select
	r.first_name,
	r.last_name,
	case (a.user_role)
		when 'O' then 'Owner'
		when 'A' then 'Admin'
		when 'R' then 'Read'
	end as user_role
from p_project p, p_project_role a, r_person r
where p.project_id = a.project_id
  and a.user_id = r.person_id
  and p.project_id = $2
order by first_name, last_name
";

// SQL: move dataset
$sql_move="
update p_dataset
set project_id = $2
where dataset_id = $1
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

if ($res = $db->query($sql_role, array($dataset_id, $project_id))) {
	echo "Missing grants in the new project:</br>";
	echo "<p><table>";
	foreach ($res as $row)
		echo "<tr><td>".$row['first_name']." ".$row['last_name']."</td><td>".
			$row['user_role']."</td></tr>";
	echo "</table></p>";
	echo "Please add grants to new project or remove any superfluous grants from dataset.<br/>";
	echo "Then please try again.";
} else {
	if ($res = $db->execute($sql_move, array($dataset_id, $project_id))) {
		echo "Dataset moved to new project.";
	}
}

$db->disconnect();
?>
