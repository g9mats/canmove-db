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
	c.capture_time,
	c.latitude,
	c.longitude,
	c.location,
	c.operator_id,
	c.remark
from d_ori_animal a, d_ori_capture c
where a.animal_id = c.animal_id
  and a.dataset_id=$1
order by a.animal, c.capture_time
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
<th>Capture Time</th>
<th>Latitude</th>
<th>Longitude</th>
<th>Location</th>
<th>Operator Id</th>
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
<td><?php echo $row['capture_time']; ?></td>
<td><?php echo $row['latitude']; ?></td>
<td><?php echo $row['longitude']; ?></td>
<td><?php echo $row['location']; ?></td>
<td><?php echo $row['operator_id']; ?></td>
<td><?php echo $row['remark']; ?></td>
</tr>
<?php
	}
?>
</table>

<?php $db->disconnect(); ?>
