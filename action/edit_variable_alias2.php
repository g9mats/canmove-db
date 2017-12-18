<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$data_alias_id=$_POST['data_alias_id'];
if ($data_alias_id=="") {
	echo "<p>You must specify a variable alias.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: select variable alias record
$sql_alias="
select
	data_id,
	header,
	remark,
	keep_alias
from r_data_alias
where data_alias_id = $1
";

// SQL: select variable records
$sql_var="
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

$res = $db->query($sql_alias, array($data_alias_id));
$row = $res[0];
?>

<p>
Variable Alias: <?php echo $row['header']; ?>
</p>

<!-- Form for update of person -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="3" type="hidden" />
	<input name="data_alias_id" value="<?php echo $data_alias_id; ?>" type="hidden" />
	<table><tr>
	<td>Variable Header:</td>
	<td><select id="data_id" name="data_id" required="required">
		<option value="" selected>Select variable alias</option>
<?php
	if ($vres = $db->query($sql_var)) {
		foreach ($vres as $vrow) {
			echo "<option value='".$vrow['data_id']."'>".
				$vrow['header']." - ".
				$vrow['data_subset']." - ".$vrow['storage_type'];
			echo "</option>";
		}
	}
?>
	</select></td>
	</tr><tr>
	<td>Alias Header:</td>
	<td><input id="aheader" name="header" required="required" /></td>
	</tr><tr>
	<td>Keep Alias:</td>
	<td><select id="keep_alias" name="keep_alias" required="required">
		<option value="false">No</option>
		<option value="true">Yes</option>
	</select></td>
	</tr><tr>
	<td>Remark:</td>
	<td><input id="remark" name="remark" /></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Save</button></td>
	</tr></table>
</form>

<script>
(function(){
	document.getElementById("data_id").value = "<?php echo $row['data_id']; ?>";
	document.getElementById("aheader").value = "<?php echo $row['header']; ?>";
	document.getElementById("keep_alias").value = "<?php if ($row['keep_alias']=="t") echo "true"; else echo "false"; ?>";
	document.getElementById("remark").value = "<?php echo $row['remark']; ?>";
})()
</script>

<?php $db->disconnect(); ?>
