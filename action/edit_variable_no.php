<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";
echo "<script>";
require_once $DBRoot."/lib/ctx_storage_subset.js";
echo "</script>";
$ajax_file=$WebRoot."/db/action/create_variable_subset.php";

// SQL: select storage types
$sql_storage="
select
	storage_type,
	storage_name
from r_storage_type
order by storage_name
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
</p>

<!-- Form for selection of storage_type and data_subset -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<table><tr>
	<td>Storage Type:</td>
	<td><select id="storage_type" name="storage_type" required="required"
			onchange="ctx_storage_subset('<?php echo $ajax_file; ?>')">
		<option value="" selected>Select storage type</option>
<?php
		if ($res = $db->query($sql_storage)) {
			foreach ($res as $row)
				echo "<option value='".$row['storage_type']."'>".$row['storage_name']."</option>";
		}
?>
	</select></td>
	</tr><tr>
	<td>Data Subset:</td>
	<td><select id="data_subset" name="data_subset" required="required">
	</select></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Next</button></td>
	</tr></table>
</form>
