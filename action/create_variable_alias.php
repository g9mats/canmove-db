<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";

// SQL: select variable record
$sql="
select
	data_id,
	storage_type,
	header,
	data_subset
from r_data
order by header, data_subset
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
</p>

<!-- Form for insert of variable -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<table><tr>
	<td>Target Variable:</td>
	<td><select name="data_id" required="required">
		<option value="" selected>Select variable</option>
<?php
		if ($res = $db->query($sql)) {
			foreach ($res as $row)
				echo "<option value='".$row['data_id']."'>".$row['header']." - ".$row['storage_type']." - ".$row['data_subset']."</option>";
		}
?>
	</select></td>
	</tr><tr>
	<td>Alias Header:</td>
	<td><input name="header" required="required" value="" /></td>
	</tr><tr>
	<td>Keep Alias:</td>
	<td><select name="keep_alias" required="required">
		<option value="false" selected>No</option>
		<option value="true">Yes</option>
	</select></td>
	</tr><tr>
	<td>Alias Remark:</td>
	<td><input name="remark" value="" /></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Save</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
