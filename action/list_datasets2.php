<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$project_id=$_POST['project_id'];
require_once $DBRoot."/lib/DBLink.php";

// SQL: select project and role records
$sql_project="
select
	p.project_id,
	p.title,
	case a.user_role
		when 'O' then 'Owner'
		when 'A' then 'Admin'
		when 'R' then 'Read'
		else a.user_role
	end user_role,
	s_project_owner(p.project_id) as owner
from p_project p, p_project_role a, r_person r
where p.project_id = a.project_id
  and a.user_id = r.person_id
  and r.drupal_id = $1
  PROJECT_COND
order by p.title
";

// SQL: select dataset and role records
$sql_dataset="
select
	d.title,
	dt.data_name data_name,
	case d.public
		when true then 'Yes'
		else 'No'
	end public,
	case a.user_role
		when 'O' then 'Owner'
		when 'A' then 'Admin'
		when 'W' then 'Write'
		when 'R' then 'Read'
		else a.user_role
	end user_role,
	s_dataset_owner(d.dataset_id) as owner
from p_dataset d, p_dataset_role a, r_person r, r_data_type dt
where d.dataset_id = a.dataset_id
  and a.user_id = r.person_id
  and d.data_type = dt.data_type
  and d.project_id = $1
  and r.drupal_id = $2
order by d.title
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
List of datasets that you have access
<?php
echo "to";
if ($project_id!="")
	echo " within specified project";
echo ".";
?>
</p>

<table>
<tr>
<th>Title</th>
<th>Data Type</th>
<th>Public</th>
<th>Role</th>
<th>Owner</th>
</tr>
<?php
// Insert demand for certain project
if ($project_id!="")
	$sql_project = str_replace("PROJECT_COND","and p.project_id = ".$project_id, $sql_project);
else
	$sql_project = str_replace("PROJECT_COND","", $sql_project);
// For every project (only one actually but be prepared for more)
if ($resP = $db->query($sql_project,array($user->uid)))
	foreach ($resP as $rowP) {
?>
<tr bgcolor="#ccecff">
<td><?php echo "Project: ".$rowP['title']; ?></td>
<td></td>
<td></td>
<td><?php echo $rowP['user_role']; ?></td>
<td><?php echo $rowP['owner']; ?></td>
</tr>
<?php
// For every dataset
if ($resD = $db->query($sql_dataset,array($rowP['project_id'],$user->uid)))
	foreach ($resD as $rowD) {
?>
<tr>
<td><?php echo "- ".$rowD['title']; ?></td>
<td><?php echo $rowD['data_name'] ?></td>
<td><?php echo $rowD['public'] ?></td>
<td><?php echo $rowD['user_role']; ?></td>
<td><?php echo $rowD['owner']; ?></td>
</tr>

<?php
	}
	}
?>
</table>

<?php $db->disconnect(); ?>
