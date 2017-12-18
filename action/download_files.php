<?php
// Creator: Mats J Svensson, CAnMove

require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
echo "<script>";
require_once $DBRoot."/lib/ctx_dataset_subset.js";
require_once $DBRoot."/lib/ctx_subset_status.js";
require_once $DBRoot."/lib/ctx_status_file.js";
echo "</script>";
$ajax_file=$WebRoot."/db/action/download_files_subset.php";
$ajax_file2=$WebRoot."/db/action/download_files_status.php";
$ajax_file3=$WebRoot."/db/action/download_files_file.php";

$sql="
select distinct
	d.dataset_id,
	d.title
from p_dataset d, p_dataset_role a, r_person p, l_file f
where d.dataset_id = a.dataset_id
  and a.user_id = p.person_id
  and d.dataset_id = f.dataset_id
  and d.storage_type in (
	select object_key from x_context
	where context_type = 'action'
	  and context_key = 'download_files'
	  and object_type = 'storage_type'
  )
  and p.drupal_id = $1
  and not f.deleted
order by d.title
";
?>

<p>
Download files from server file archive.
</p>
<p>
You must select dataset and then data subset and data status before you can select files. You will only find those datasets that you have access to and for which there are files in the server file archive. (If you are using Internet Explorer you can only download one file at a time.)
</p>

<!-- Form for selection of dataset -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<table><tr>
	<td>Dataset:</td>
	<td><select id="dataset_id" name="dataset_id" required="required"
			onchange="ctx_dataset_subset('<?php echo $ajax_file; ?>');
					ctx_subset_status('<?php echo $ajax_file2; ?>');
					ctx_status_file('<?php echo $ajax_file3; ?>')">
		<option value="" selected>Select dataset</option>
<?php
	if ($res = $db->query($sql, array($user->uid))) {
		foreach ($res as $row)
			echo "<option value='".$row['dataset_id']."'>".
				$row['title']."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td>Data Subset:</td>
	<td><select id="data_subset" name="data_subset" required="required"
			onchange="ctx_subset_status('<?php echo $ajax_file2; ?>');
					ctx_status_file('<?php echo $ajax_file3; ?>')">
	</select></td>
	</tr><tr>
	<td>Data Status:</td>
	<td><select id="data_status" name="data_status" required="required"
			onchange="ctx_status_file('<?php echo $ajax_file3; ?>')">
	</select></td>
	</tr><tr>
	<td><span id="file_label">Files:</span></td>
	<td><select id="file_arr[]" name="file_arr[]" required="required" multiple="multiple" size="10">
	</select></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Download</button></td>
	</tr><tr>
	</tr></table>
</form>
<script>
var isIE = /*@cc_on!@*/false || !!document.documentMode;
if (isIE) {
	document.getElementById("file_arr[]").multiple="";
	document.getElementById("file_arr[]").size="1";
	document.getElementById("file_label").innerHTML="File:";
}
</script>

<?php $db->disconnect(); ?>
