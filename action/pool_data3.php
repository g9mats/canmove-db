<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_arr=$_POST['dataset_arr'];
if (count($dataset_arr)==0) {
	echo "<p>You must specify at least one dataset.</p>";
	return;
}
$dataset_json=json_encode($dataset_arr);
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

require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL: select variables
$sql_var="
select
	c.header,
	count(*) as vnum
from p_column c, r_data d
where c.data_id = d.data_id
  and c.dataset_id in (".implode(',',$dataset_arr).")
  and c.data_subset = $1
group by d.order_no, c.header
order by d.order_no, c.header, vnum desc
";

// SQL: check if storage type uses static variables
$sql_chk_static="
select 1
from x_context
where context_type = 'property'
  and context_key = 'static_variables'
  and object_type = 'storage_type'
  and object_key = $1
";

// SQL: select variables for storage types with static variables
$sql_var_static="
select
	header,
	count(*) as vnum
from p_dataset c, r_data d
where c.dataset_id in (".implode(',',$dataset_arr).")
  and d.storage_type = $1
  and d.data_subset = $2
group by d.order_no, d.header
order by d.order_no, d.header, vnum desc
";

$sql_tz="
select replace(name,'Etc/','') as name,utc_offset
from pg_timezone_names
where name not like 'GMT%'
  and name not like 'posix%'
  and name not like 'UTC%'
  and name not like '%UCT%'
order by name
";
?>

<p>
Select one or more variables. Variables marked with * are present in all datasets and therefore pre-selected.
</p>

<!-- Form for selection of dataset -->
<form action="/db/action/pool_data4.php" method="post">
	<input name="storage_type" value="<?php echo $storage_type; ?>" type="hidden" />
	<input name="data_subset" value="<?php echo $data_subset; ?>" type="hidden" />
	<input name="dataset_json" value='<?php echo $dataset_json; ?>' type="hidden" />
	<table><tr>
	<td>Variables:</td>
	<td><select id="var_arr[]" name="var_arr[]" required="required" multiple="multiple" size="20">
		<option value="">Select variables</option>
<?php
	if ($res = $db->query($sql_chk_static, array($storage_type))) {
		$sql_var = $sql_var_static;
		$res = $db->query($sql_var, array($storage_type,$data_subset));
	} else
		$res = $db->query($sql_var, array($data_subset));
	if ($res) {
		$dnum = count($dataset_arr);
		foreach ($res as $row) {
			$vnum = $row['vnum'];
			if ($vnum == $dnum)
				echo "<bold>";
			echo "<option value='".$row['header']."'";
			if ($vnum == $dnum)
				echo " selected='selected'";
			echo ">".$row['header'];
			if ($vnum == $dnum)
				echo " *";
			echo "</option>";
			if ($vnum == $dnum)
				echo "</bold>";
		}
	}
?>
	</select></td>
	</tr><tr>
	<td id="tzlabel">Time Zone:</td>
	<td><select id="tz" name="tz" required="required">
		<option value="" selected>Select time zone</option>
		<option value="Europe/Stockholm">Europe/Stockholm</option>
		<option value="Etc/UTC">UTC: 00:00:00</option>
<?php
	if ($res = $db->query($sql_tz)) {
		foreach ($res as $row) {
			echo "<option value='".$row['name']."'>".
				$row['name'].": ".$row['utc_offset']."</option>";
		}
	}
?>
	</select></td>
	</tr><tr>
	<td></td>
	<td>Please be patient while data is extracted.</td>
	</tr><tr>
	<td></td>
	<td><button id="goon" type="submit">Extract</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
