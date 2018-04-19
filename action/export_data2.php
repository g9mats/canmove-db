<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
$data_subset=$_POST['data_subset'];
if ($data_subset=="") {
	echo "<p>You must specify a data subset.</p>";
	return;
}
$version=$_POST['version'];
$varset=$_POST['varset'];
$tz=$_POST['tz'];
if ($tz=="") {
	echo "<p>You must specify a time zone.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: select column records
$sql_col="
select
	order_no,
	header
from p_column
where dataset_id = $1
  and data_subset = $2
  and varset = $3
order by order_no
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
Select at least one column in the listbox.
</p>

<!-- Form for selection of columns -->
<form action="/db/action/export_data3.php" method="post">
	<input name="dataset_id" value="<?php echo $dataset_id; ?>" type="hidden" />
	<input name="data_subset" value="<?php echo $data_subset; ?>" type="hidden" />
	<input name="version" value="<?php echo $version; ?>" type="hidden" />
	<input name="varset" value="<?php echo $varset; ?>" type="hidden" />
	<input name="tz" value="<?php echo $tz; ?>" type="hidden" />
	<table><tr>
	<td>Columns:</td>
	<td><select id="column_arr[]" name="column_arr[]" required="required" multiple="multiple" size="10">
		<option value="" selected>Select columns</option>
<?php
	$vnum=2;
	if ($res = $db->query($sql_col, array($dataset_id,$data_subset,$varset))) {
		foreach ($res as $row) {
			echo "<option value='".$row['order_no']."'>".$row['header'].
				"</option>";
			$vnum++;
		}
	}
    $vnum=min($vnum,10);
?>
	</select></td>
	</tr><tr>
	<td></td>
	<td>Please be patient while data is exported.</td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Export</button></td>
	</tr></table>
</form>
<script>
    document.getElementById("column_arr[]").size="<?php echo $vnum; ?>";
</script>

<?php $db->disconnect(); ?>
