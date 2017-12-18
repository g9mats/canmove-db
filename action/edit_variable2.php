<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$data_id=$_POST['data_id'];
if ($data_id=="") {
	echo "<p>You must specify a variable.</p>";
	return;
}
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

// SQL: select variable record
$sql_var="
select
	data_id,
	storage_type,
	data_subset,
	table_name,
	column_name,
	column_type,
	data_type,
	case_type,
	mandatory,
	nullable,
	load_name,
	header,
	order_no,
	unit,
	remark
from r_data
where data_id = $1
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$res = $db->query($sql_var, array($data_id));
$row = $res[0];

?>

<p>
Variable: <?php echo $row['header']." [".$row['data_id']."]"; ?>
</p>

<!-- Form for update of person -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="3" type="hidden" />
	<input name="data_id" value="<?php echo $data_id; ?>" type="hidden" />
	<table><tr>
	<td>Storage Type:</td>
	<td><select id="storage_type" name="storage_type" required="required"
			onchange="ctx_storage_subset('<?php echo $ajax_file; ?>');
						ctx_subset_table('<?php echo $ajax_file2; ?>');
						ctx_table_column('<?php echo $ajax_file3; ?>')">
		<option value="" selected>Select storage type</option>
<?php
		if ($res2 = $db->query($sql_storage)) {
			foreach ($res2 as $row2)
				echo "<option value='".$row2['storage_type']."'>".$row2['storage_name']."</option>";
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
	<td><select id="column_type" name="column_type" required="required">
		<option value="fix">Fix</option>
		<option value="var">Variable</option>
	</select></td>
	</tr><tr>
	<td>Data Type:</td>
	<td><select id="data_type" name="data_type" required="required">
		<option value="boolean">Boolean</option>
		<option value="datetime">DateTime</option>
		<option value="float">Float</option>
		<option value="integer">Integer</option>
		<option value="text">Text</option>
	</select></td>
	</tr><tr>
	<td>Case Type:</td>
	<td><select id="case_type" name="case_type" required="required">
		<option value="lower">Lowercase</option>
		<option value="mixed">Mixed</option>
		<option value="upper">Uppercase</option>
	</select></td>
	</tr><tr>
	<td>Mandatory:</td>
	<td><select id="mandatory" name="mandatory" required="required">
		<option value="false">No</option>
		<option value="true">Yes</option>
	</select></td>
	</tr><tr>
	<td>Nullable:</td>
	<td><select id="nullable" name="nullable" required="required">
		<option value="false">No</option>
		<option value="true">Yes</option>
	</select></td>
	</tr><tr>
	<td>Load Name:</td>
	<td><input id="load_name" name="load_name" required="required" /></td>
	</tr><tr>
	<td>Header:</td>
	<td><input id="xheader" name="header" required="required" /></td>
	</tr><tr>
	<td>Order No:</td>
	<td><input id="order_no" name="order_no" size=5 /></td>
	</tr><tr>
	<td>Unit:</td>
	<td><input id="unit" name="unit" /></td>
	</tr><tr>
	<td>Remark:</td>
	<td><input id="remark" name="remark" size=60 /></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Save</button></td>
	</tr></table>
</form>

<script>
(function(){
	document.getElementById("storage_type").value = "<?php echo $row['storage_type']; ?>";
	ctx_storage_subset('<?php echo $ajax_file; ?>');
	ctx_subset_table('<?php echo $ajax_file2; ?>');
	document.getElementById("data_subset").value = "<?php echo $row['data_subset']; ?>";
	document.getElementById("table_name").value = "<?php echo $row['table_name']; ?>";
	ctx_table_column('<?php echo $ajax_file3; ?>');
	document.getElementById("column_name").value = "<?php echo $row['column_name']; ?>";
	document.getElementById("column_type").value = "<?php echo $row['column_type']; ?>";
	document.getElementById("data_type").value = "<?php echo $row['data_type']; ?>";
	document.getElementById("case_type").value = "<?php echo $row['case_type']; ?>";
	document.getElementById("mandatory").value = "<?php if ($row['mandatory']=="t") echo "true"; else echo "false"; ?>";
	document.getElementById("nullable").value = "<?php if ($row['nullable']=="t") echo "true"; else echo "false"; ?>";
	document.getElementById("load_name").value = "<?php echo $row['load_name']; ?>";
	document.getElementById("xheader").value = "<?php echo $row['header']; ?>";
	document.getElementById("order_no").value = "<?php echo $row['order_no']; ?>";
	document.getElementById("unit").value = "<?php echo $row['unit']; ?>";
	document.getElementById("remark").value = "<?php echo $row['remark']; ?>";
})()
</script>

<?php $db->disconnect(); ?>
