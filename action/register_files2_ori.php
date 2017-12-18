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
if (!isset($version)) $version=1;
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
echo "<script>";
require_once $DBRoot."/lib/ctx_file_info_ori.js";
echo "</script>";
$ajax_file=$WebRoot."/db/action/register_files_info.php";

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

// SQL: select versions
$sql_ver="
select max(version) as maxver
from l_file
where dataset_id = $1
";
?>

<p>
Select file and version in the listboxes.
</p>

<!-- Form for selection of file -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="3" type="hidden" />
	<input name="dataset_id" value="<?php echo $dataset_id; ?>" type="hidden" />
	<input name="data_subset" value="<?php echo $data_subset; ?>" type="hidden" />
	<table><tr>
	<td>Files:</td>
	<td><select id="file_id" name="file_id" required="required"
			onchange="ctx_file_info('<?php echo $ajax_file; ?>')">
		<option value="" selected="selected">Select file</option>
<?php
	if ($res = $db->query($sql_file, array($dataset_id,$data_subset))) {
		foreach ($res as $row) {
			echo "<option value='".$row['file_id']."'>".
				$row['original_name']." - ".
				$row['upload_time'].
				"</option>";
		}
	}
?>
	</select></td>
	</tr><tr>
	<td>Version:</td>
	<td><select id="version" name="version" required="required">
<?php
	if ($res = $db->query($sql_ver, array($dataset_id))) {
		for ($i=1; $i<=$res[0]['maxver']; $i++) {
			echo "<option value='".$i."'";
			if ($i == $version)
				echo " selected='selected'";
			echo ">".$i."</option>";
		}
		echo "<option value='".$i."'>".$i."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td>Remark:</td>
	<td><input id="remark" name="remark" size="75" /></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Register</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
