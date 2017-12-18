<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";

// SQL: select variable records
$sql="
select
	data_id,
	storage_type,
	data_subset,
	header
from r_data
order by header, data_subset, storage_type
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
</p>

<!-- Form for selection of variable -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<table><tr>
	<td>Variable:</td>
	<td><select name="data_id" required="required">
		<option value="" selected>Select variable</option>
<?php
	if ($res = $db->query($sql)) {
		foreach ($res as $row) {
			echo "<option value='".$row['data_id']."'>".$row['header']." - ".
				$row['data_subset']." - ".$row['storage_type'];
			echo "</option>";
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
