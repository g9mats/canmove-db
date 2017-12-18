<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";

// SQL: select device model record
$sql="
select
	model,
	manufacturer
from r_device_model
order by model
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
</p>

<!-- Form for selection of device model -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<table><tr>
	<td>Model:</td>
	<td><select name="model" required="required">
		<option value="" selected>Select device model</option>
<?php
	if ($res = $db->query($sql)) {
		foreach ($res as $row) {
			echo "<option value='".$row['model']."'>".$row['model'].
				" - ".$row['manufacturer']."</option>";
		}
	}
?>
	</select></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Edit</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
