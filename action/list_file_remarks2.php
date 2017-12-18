<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$sql="
select
	initcap(data_status) as data_status,
	original_name,
	remark
from l_file
where dataset_id = $1
  and not deleted
order by 1, 2
";
?>

<p>
</p>

<table border="1">
<tr>
<th>Status</th>
<th>File Name</th>
<th>Remark</th>
</tr>
<?php
if ($res = $db->query($sql, array($dataset_id)))
	foreach ($res as $row) {
?>
<tr>
<td><?php echo $row['data_status']; ?></td>
<td><?php echo $row['original_name']; ?></td>
<td><?php echo $row['remark']; ?></td>
</tr>
<?php
}
?>
</table>

<?php $db->disconnect(); ?>
