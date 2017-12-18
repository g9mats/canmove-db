<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$find=$_POST['find'];
$showds=$_POST['showds'];
if ($showds=="") {
	echo "<p>You must specify whether to show datasets.</p>";
	return;
}
$order=$_POST['order'];
if ($order=="") {
	echo "<p>You must specify a sort order.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: select projects
$sql_project="
select
	project_id,
	title,
	s_project_owner(project_id,'<br/>') as owner
from p_project
where upper(title||' '||s_project_owner(project_id)) like upper('%'||$1||'%')
order by ORDER
";

// SQL: select datasets
$sql_dataset="
select
	ds.title,
	s_dataset_owner(ds.dataset_id,'<br/>') as owner,
	d.data_name data_name
from p_dataset ds, r_data_type d
where ds.data_type = d.data_type
  and ds.project_id = $1
order by ds.title
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
List of all
<?php
echo "projects";
if ($showds=="y") {
	echo ", with datasets";
	if ($find!="")
		echo ",";
}
if ($find!="")
	echo " matching search text: ".$find;
echo ".";
?>
</p>

<table>
<tr>
<th>Title</th>
<?php
if ($showds=="y") {
	echo "<th>Data Type</th>";
}
?>
<th>Owner</th>
</tr>

<?php
// Change order by clause
$sql_project = str_replace("ORDER",$order,$sql_project);
// For every project
if ($pres = $db->query($sql_project,array($find)))
	foreach ($pres as $prow) {
		if ($showds=="y")
			//echo "<tr bgcolor='#ccecff'>";
			//echo "<tr bgcolor='#b9d3dc'>";
			echo "<tr bgcolor='#dce9ee'>";
		else
			echo "<tr>";
		echo "<td>".$prow['title']."</td>";
		if ($showds=="y") echo "<td></td>";
		echo "<td>".$prow['owner']."</td>";
		echo "</tr>";
		if ($showds=="y") {
			if ($dres = $db->query($sql_dataset,array($prow['project_id'])))
				foreach ($dres as $drow) {
					echo "</tr>";
					echo "<td>- ".$drow['title']."</td>";
					echo "<td>".$drow['data_name']."</td>";
					echo "<td>".$drow['owner']."</td>";
					echo "<tr>";
				}
		}
	}
?>
</table>

<?php $db->disconnect(); ?>
