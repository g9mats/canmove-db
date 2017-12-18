<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";
echo "<script>";
require_once $DBRoot."/lib/ctx_storage_subset.js";
require_once $DBRoot."/lib/ctx_storage_table.js";
require_once $DBRoot."/lib/ctx_table_column.js";
echo "</script>";
$ajax_file=$WebRoot."/db/action/create_variable_subset.php";
$ajax_file2=$WebRoot."/db/action/create_variable_table.php";
$ajax_file3=$WebRoot."/db/action/create_variable_column.php";

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

<!-- Form for insert of variable -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<table><tr>
	<td>Storage Type:</td>
	<td><select id="storage_type" name="storage_type" required="required"
			onchange="ctx_storage_subset('<?php echo $ajax_file; ?>');
						ctx_subset_table('<?php echo $ajax_file2; ?>');
						ctx_table_column('<?php echo $ajax_file3; ?>')">
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
	<td>Table Name:</td>
	<td><select id="table_name" name="table_name" required="required"
			onchange="ctx_table_column('<?php echo $ajax_file3; ?>')">
	</select></td>
	</tr><tr>
	<td>Column Name:</td>
	<td><select id="column_name" name="column_name" required="required">
	</select></td>
	</tr><tr>
	<td>Column Type:</td>
	<td><select name="column_type" required="required">
		<option value="fix">Fix</option>
		<option value="var" selected>Variable</option>
	</select></td>
	</tr><tr>
	<td>Data Type:</td>
	<td><select name="data_type" required="required">
		<option value="boolean">Boolean</option>
		<option value="datetime">DateTime</option>
		<option value="float">Float</option>
		<option value="integer" selected>Integer</option>
		<option value="text">Text</option>
	</select></td>
	</tr><tr>
	<td>Case Type:</td>
	<td><select name="case_type" required="required">
		<option value="lower">Lowercase</option>
		<option value="mixed" selected>Mixed</option>
		<option value="upper">Uppercase</option>
	</select></td>
	</tr><tr>
	<td>Mandatory:</td>
	<td><select name="mandatory" required="required">
		<option value="false" selected>No</option>
		<option value="true">Yes</option>
	</select></td>
	</tr><tr>
	<td>Nullable:</td>
	<td><select name="nullable" required="required">
		<option value="false">No</option>
		<option value="true" selected>Yes</option>
	</select></td>
	</tr><tr>
	<td>Load Name:</td>
	<td><input name="load_name" required="required" value="c" /></td>
	</tr><tr>
	<td>Header:</td>
	<td><input name="header" required="required" value="" /></td>
	</tr><tr>
	<td>Unit:</td>
	<td><input name="unit" value="" /></td>
	</tr><tr>
	<td>Remark:</td>
	<td><input name="remark" value="" /></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Save</button></td>
	</tr></table>
</form>
