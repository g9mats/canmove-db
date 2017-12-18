<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";

// SQL: select project and role records
$sql="
select
	p.title,
	s_project_owner(p.project_id) as owner,
	case a.user_role
		when 'O' then 'Owner'
		when 'A' then 'Admin'
		when 'R' then 'Read'
		else a.user_role
	end user_role,
	p.remark
from p_project p, p_project_role a, r_person r
where p.project_id = a.project_id
  and a.user_id = r.person_id
  and r.drupal_id = $1
order by p.title
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
List of projects that you have access to.
</p>

<table>
<tr>
<th>Title</th>
<th>Owner</th>
<th>Role</th>
<th>Remark</th>
</tr>

<?php // For every project
if ($res = $db->query($sql,array($user->uid)))
	foreach ($res as $row) {
?>
<tr>
<td><?php echo $row['title']; ?></td>
<td><?php echo $row['owner']; ?></td>
<td><?php echo $row['user_role']; ?></td>
<td><?php echo $row['remark']; ?></td>
</tr>
<?php
	}
?>

</table>

<?php $db->disconnect(); ?>
