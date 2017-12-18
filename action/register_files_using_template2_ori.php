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
require_once $DBRoot."/lib/DBLink.php";

// SQL: select storage type of the dataset
$sql_storage_type="
select storage_type
from p_dataset
where dataset_id = $1
";

// SQL: select file records
$sql_file="
select
	file_id,
	original_name,
	upload_time
from l_file
where dataset_id = $1
  and data_subset = $2
  and data_status = 'final'
  and not registered
  and not deleted
order by original_name
";

// SQL: select ORI file name templates
$sql_template="
select
	template_text
from x_file_name_template
where storage_type = $1
  and data_subset = $2
order by order_no
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$res = $db->query($sql_storage_type, array($dataset_id));
$storage_type = $res[0]['storage_type'];
?>

<p>
Select files and template in the listboxes.
</p>

<!-- Form for selection of file -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="3" type="hidden" />
	<input name="dataset_id" value="<?php echo $dataset_id; ?>" type="hidden" />
	<table><tr>
	<td>Files:</td>
	<td><select id="file_arr[]" name="file_arr[]" required="required" multiple="multiple" size="11">
<?php
	$fnum=1;
	if ($res = $db->query($sql_file, array($dataset_id,$data_subset))) {
		foreach ($res as $row) {
			echo "<option value='".$row['file_id']."'>".
				$row['original_name']." - ".
				$row['upload_time'].
				"</option>";
			$fnum++;
		}
	}
	$fnum=min($fnum,11);
?>
	</select></td>
	</tr><tr>
	<td>Prefix:</td>
	<td><input name="prefix" /></td>
	</tr><tr>
	<td>Template:</td>
	<td><select name="template_text" required="required">
<?php
	if ($res = $db->query($sql_template, array($storage_type,$data_subset))) {
		foreach ($res as $row)
			echo "<option value='".$row['template_text']."'>".
				$row['template_text']."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td>Suffix:</td>
	<td><input name="suffix" value=".txt" /></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Register</button></td>
	</tr></table>
</form>
<script>
	document.getElementById("file_arr[]").size="<?php echo $fnum; ?>";
</script>

<?php $db->disconnect(); ?>
