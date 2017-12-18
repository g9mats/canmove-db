<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$find=$_POST['find'];
require_once $DBRoot."/lib/DBLink.php";

// SQL: select device model records
$sql="
select
	device_model_id,
	model,
	manufacturer,
	description,
	weight
from r_device_model
where upper(model||' '||coalesce(manufacturer,'-')) like
		upper('%'||$1||'%')
order by model
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
</p>

<table>
<tr>
<th>Id</th>
<th>Model</th>
<th>Manufacturer</th>
<th>Weight</th>
<th>Description</th>
</tr>

<?php // For every device model
if ($res = $db->query($sql,array($find)))
	foreach ($res as $row) {
?>
<tr>
<td><?php echo $row['device_model_id']; ?></td>
<td><?php echo $row['model']; ?></td>
<td><?php echo $row['manufacturer']; ?></td>
<td><?php echo $row['weight']; ?></td>
<td><?php echo $row['description']; ?></td>
</tr>
<?php
	}
?>

</table>

<?php $db->disconnect(); ?>
