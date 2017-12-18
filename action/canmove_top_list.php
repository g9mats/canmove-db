<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";

// SQL: select statistics per owner
$sql="
select
	s_project_owner(p.project_id) as owner,
	count(distinct p.project_id) as projects,
	count(distinct d.dataset_id) as datasets,
	count(distinct p.project_id)*2+count(distinct dataset_id) as points
from p_project p, p_dataset d
where p.project_id = d.project_id
group by 1
order by 4 desc, 2 desc, 1
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
CAnMove Top List of database contributors.
</p>

<table>
<tr>
<th>Owner</th>
<th>Projects</th>
<th>Datasets</th>
<th>Points</th>
</tr>

<?php
if ($res = $db->query($sql))
	foreach ($res as $row) {
?>
<tr>
<td><?php echo $row['owner']; ?></td>
<td style="text-align:center"><?php echo $row['projects']; ?></td>
<td style="text-align:center"><?php echo $row['datasets']; ?></td>
<td style="text-align:center"><?php echo $row['points']; ?></td>
</tr>
<?php
	}
?>
</table>

<?php $db->disconnect(); ?>
