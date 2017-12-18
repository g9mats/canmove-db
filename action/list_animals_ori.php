<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// Build SQL select statement for variables
$sql="
select
	animal,
	taxon,
	remark
from d_ori_animal
where dataset_id=$1
order by animal
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
</p>

<table border="1">
<tr>
<th>Animal Id</th>
<th>Taxon</th>
<th>Remark</th>
</tr>
<?php
// Get animal information and present it in a table
if ($res = $db->query($sql,array($dataset_id)))
	foreach ($res as $row) {
?>
<tr>
<td><?php echo $row['animal']; ?></td>
<td><?php echo $row['taxon']; ?></td>
<td><?php echo $row['remark']; ?></td>
</tr>
<?php
	}
?>
</table>

<?php $db->disconnect(); ?>
