<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
echo "<script>";
require_once $DBRoot."/lib/ctx_file_remark.js";
echo "</script>";
$ajax_file=$WebRoot."/db/action/edit_file_remarks_sub.php";

// SQL: select file records
$sql_file="
select
	file_id,
	initcap(data_subset) as data_subset,
	initcap(data_status) as data_status,
	original_name,
	upload_time
from l_file
where dataset_id = $1
order by original_name, data_subset, data_status
";
?>

<p>
Select file in the listbox.
</p>

<!-- Form for selection of file -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="3" type="hidden" />
	<input name="dataset_id" value="<?php echo $dataset_id; ?>" type="hidden" />
	<table><tr>
	<td>Files:</td>
	<td><select id="file_id" name="file_id" required="required"
			onchange="ctx_file_remark('<?php echo $ajax_file; ?>')">
		<option value="" selected="selected">Select file</option>
<?php
	if ($res = $db->query($sql_file, array($dataset_id))) {
		foreach ($res as $row) {
			echo "<option value='".$row['file_id']."'>".
				$row['original_name']." - ".
				$row['data_subset']." - ".
				$row['data_status']." - ".
				$row['upload_time'].
				"</option>";
		}
	}
?>
	</select></td>
	</tr><tr>
	<td>Remark:</td>
	<td><input id="remark" name="remark" size="75" /></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Save</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
