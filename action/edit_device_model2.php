<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$model=$_POST['model'];
if ($model=="") {
	echo "<p>You must specify a device model.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: select device model record
$sql="
select
	device_model_id,
	model,
	manufacturer,
	description,
	weight
from r_device_model
where model = $1
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$res = $db->query($sql, array($model));
$row = $res[0];
?>

<p>
</p>

<!-- Form for update of device model -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="3" type="hidden" />
	<input name="device_model_id" value="<?php echo $row['device_model_id']; ?>" type="hidden" />
	<table><tr>
	<td>Model:</td>
	<td><input name="model" required="required" size="30"
		value="<?php echo $row['model'] ?>" /></td>
	</tr><tr>
	<td>Manufacturer:</td>
	<td><input name="manufacturer" size="30"
		value="<?php echo $row['manufacturer'] ?>" /></td>
	</tr><tr>
	<td>Weight:</td>
	<td><input name="weight" size="8"
		value="<?php echo $row['weight'] ?>" /></td>
	</tr><tr>
	<td>Description:</td>
	<td><input name="description" size="50"
		value="<?php echo $row['description'] ?>" /></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Save</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
