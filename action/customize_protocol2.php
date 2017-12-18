<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$storage_type=$_POST['storage_type'];
if ($storage_type=="") {
	echo "<p>You must specify a storage type.</p>";
	return;
}
$data_subset=$_POST['data_subset'];
if ($data_subset=="") {
	echo "<p>You must specify a data subset.</p>";
	return;
}
$help_text=$_POST['help_text'];
if ($help_text=="") {
	echo "<p>You must specify a help text choice.</p>";
	return;
}

require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL: select variables
$sql_var="
select
	order_no,
	header,
	mandatory
from r_data
where storage_type = $1
  and data_subset = $2
union
select
	d.order_no,
	a.header,
	false
from r_data d, r_data_alias a
where d.data_id = a.data_id
  and d.storage_type = $1
  and d.data_subset = $2
order by order_no, header
";
?>

<p>
Select variables. Variables marked with * are mandatory and therefore pre-selected.
</p>

<!-- Form for selection of dataset -->
<form action="/db/action/customize_protocol3.php" method="post">
	<input name="storage_type" value="<?php echo $storage_type; ?>" type="hidden" />
	<input name="data_subset" value="<?php echo $data_subset; ?>" type="hidden" />
	<input name="help_text" value="<?php echo $help_text; ?>" type="hidden" />
	<table><tr>
	<td>Variables:</td>
	<td><select id="var_arr[]" name="var_arr[]" required="required" multiple="multiple" size="30">
		<option value="">Select variables</option>
<?php
	$res = $db->query($sql_var, array($storage_type,$data_subset));
	if ($res) {
		foreach ($res as $row) {
			$mandatory = $row['mandatory'];
			echo "<option value='".$row['header']."'";
			if ($mandatory == "t")
				echo " selected='selected'";
			echo ">".$row['header'];
			if ($mandatory == "t")
				echo " *";
			echo "</option>";
			if ($mandatory == "t")
				echo "</bold>";
		}
	}
?>
	</select></td>
	</tr><tr>
	<td></td>
	<td>Please be patient while data is extracted.</td>
	</tr><tr>
	<td></td>
	<td><button id="goon" type="submit">Create</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
