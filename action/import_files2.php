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

// SQL: select file records
$sql_file="
select
	f.file_id,
	f.version,
	f.period,
	f.original_name,
	f.upload_time,
	s.versions
from p_dataset d, l_file f, r_data_subset s
where d.dataset_id = f.dataset_id
  and d.storage_type = s.storage_type
  and f.data_subset = s.data_subset
  and d.dataset_id = $1
  and f.data_subset = $2
  and f.data_status = 'final'
  and (not s.register or f.registered)
  and not f.imported
  and not f.deleted
order by f.version, f.period, f.original_name
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
Select at least one file in the listbox.
</p>

<!-- Form for selection of file -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="3" type="hidden" />
	<input name="dataset_id" value="<?php echo $dataset_id; ?>" type="hidden" />
	<input name="data_subset" value="<?php echo $data_subset; ?>" type="hidden" />
	<table><tr>
	<td>Files:</td>
	<td><select name="file_arr[]" required="required" multiple="multiple">
		<option value="" selected>Select files</option>
<?php
	if ($res = $db->query($sql_file, array($dataset_id,$data_subset))) {
		foreach ($res as $row) {
			echo "<option value='".$row['file_id']."'>";
			if ($row['versions']=="t") {
				echo $row['version']."; ";
				if ($row['period'] != "-")
					echo $row['period']."; ";
			}
			echo $row['original_name']." - ".
				$row['upload_time'].
				"</option>";
		}
	}
?>
	</select></td>
	</tr><tr>
	<td>Separator:</td>
	<td><select name="separator" required="required">
		<option value="tab" selected>tab</option>
		<option value="comma">comma</option>
	</select></td>
	</tr><tr>
	<td></td>
	<td>Please be patient while files are imported.</td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Import</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
