<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";

// SQL: select variable alias record
$sql="
select
	a.data_alias_id,
	a.header as aheader,
	d.storage_type,
	d.data_subset,
	d.header as vheader
from r_data d, r_data_alias a
where d.data_id = a.data_id
order by a.header, d.header, d.data_subset, d.storage_type
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
</p>

<!-- Form for selection of variable alias -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<table><tr>
	<td>Variable Alias:</td>
	<td><select name="data_alias_id" required="required">
		<option value="" selected>Select variable alias</option>
<?php
	if ($res = $db->query($sql)) {
		foreach ($res as $row) {
			echo "<option value='".$row['data_alias_id']."'>".
				$row['aheader'].": ".$row['vheader']." - ".
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
