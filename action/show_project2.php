<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$project_id=$_POST['project_id'];
if ($project_id=="") {
	echo "<p>You must specify a project.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: select project
$sql_project="
select
	pr.title pn,
	pr.remark
from p_project pr
where project_id = $1
";

// SQL: select project role
$sql_role="
select
	p.first_name||' '||p.last_name as name,
	case a.user_role
		when 'O' then 'Owner'
		when 'A' then 'Admin'
		when 'R' then 'Read'
		else a.user_role
	end user_role
from p_project_role a, r_person p
where a.user_id = p.person_id
  and project_id = $1
order by p.last_name, p.first_name
";

// SQL: select datasets
$sql_dataset="
select
	d.title,
	r.data_name,
	s_dataset_owner(d.dataset_id) as owner
from p_dataset d, r_data_type r
where d.data_type = r.data_type
  and d.project_id = $1
order by d.title
";

// Get project information and present it in a table
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
if ($res = $db->query($sql_project, array($project_id))) {
	$row = $res[0];
?>

<p><b>Project information</b></p>

<table>
<tr><td>Project Id:</td><td><?php echo $project_id; ?></td></tr>
<tr><td>Title:</td><td><?php echo $row['pn']; ?></td></tr>
<tr><td>Remark:</td><td><?php echo $row['remark']; ?></td></tr>
</table>
<?php
}
?>

<p></p>

<table>
<tr>
<th>User</th>
<th>Role</th>
</tr>
<?php
if ($res = $db->query($sql_role, array($project_id)))
	foreach ($res as $row) {
?>
<tr>
<td><?php echo $row['name'] ?></td>
<td><?php echo $row['user_role'] ?></td>
</tr>
<?php
}
?>
</table>

<p></p>

<table>
<tr>
<th>Dataset</th>
<th>Data Type</th>
<th>Owner</th>
</tr>
<?php
if ($res = $db->query($sql_dataset, array($project_id)))
	foreach ($res as $row) {
?>
<tr>
<td><?php echo $row['title'] ?></td>
<td><?php echo $row['data_name'] ?></td>
<td><?php echo $row['owner'] ?></td>
</tr>
<?php
}
?>
</table>

<?php $db->disconnect(); ?>
