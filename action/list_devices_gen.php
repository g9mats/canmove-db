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
	a.animal,
	a.taxon,
	d.order_no,
	d.device,
	m.model,
	d.remark
from d_gen_animal a, d_gen_track t,
	d_gen_device d left outer join r_device_model m on
		d.device_model_id = m.device_model_id
where a.animal_id = t.animal_id
  and t.track_id = d.track_id
  and a.dataset_id=$1
order by a.animal, d.order_no
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
<th>No</th>
<th>Device Id</th>
<th>Device Model</th>
<th>Remark</th>
</tr>
<?php
// Get capture information and present it in a table
if ($res = $db->query($sql,array($dataset_id)))
	foreach ($res as $row) {
?>
<tr>
<td><?php echo $row['animal']; ?></td>
<td><?php echo $row['taxon']; ?></td>
<td><?php echo $row['order_no']; ?></td>
<td><?php echo $row['device']; ?></td>
<td><?php echo $row['model']; ?></td>
<td><?php echo $row['remark']; ?></td>
</tr>
<?php
	}
?>
</table>

<?php $db->disconnect(); ?>
