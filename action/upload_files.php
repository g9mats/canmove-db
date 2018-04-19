<?php
// Creator: Mats J Svensson, CAnMove

require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
echo "<script>";
require_once $DBRoot."/lib/ctx_dataset_subset.js";
echo "</script>";
$ajax_file=$WebRoot."/db/action/upload_files_subset.php";

$sql_dataset="
select
	d.dataset_id,
	d.title
from p_dataset d, p_dataset_role a, r_person p
where d.dataset_id = a.dataset_id
  and a.user_id = p.person_id
  and d.storage_type in (
  	select object_key from x_context
	where context_type = 'action'
	  and context_key = 'upload_files'
	  and object_type in ('storage_type','data_subset')
  )
  and a.user_role in ('O','A','W')
  and p.drupal_id = $1
order by d.title
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
Upload files to server file archive.
</p>
<p>
You must select dataset before you can select data subset.
You will only find those datasets that you have write access to and with a data type that is supported by this upload utility.
</p>
<p>
Final files should be text files with fields separated by tab or comma, ready for import into the database.
Interim, raw and document files could be any kind of files that you want to store in the archive.
</p>
<p>
Time zone must be selected for final files.
All time zones in the format [continent]/[city] will handle any daylight savings time (DST) if applicable.
If your data has been collected with a device that do not use DST you should choose UTC or any time zone using GMT as a base.
Please keep in mind that the time zone index in the GMT names indicate hours west of GMT.
The real offset is shown to the right in the list and includes any active DST in the calculation.
</p>

<!-- Form for selection of dataset -->
<form action="<?php echo $DrAction ?>" method="post"
	enctype="multipart/form-data">
	<input name="next_step" value="2" type="hidden" />
	<table><tr>
	<td>Dataset:</td>
	<td><select id="dataset_id" name="dataset_id" required="required"
			onchange="ctx_dataset_subset('<?php echo $ajax_file; ?>')">
		<option value="" selected>Select dataset</option>
<?php
	if ($res = $db->query($sql_dataset, array($user->uid))) {
		foreach ($res as $row)
			echo "<option value='".$row['dataset_id']."'>".
				$row['title']."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td>Data Subset:</td>
	<td><select id="data_subset" name="data_subset" required="required">
	</select></td>
	</tr><tr>
	<td>Data Status:</td>
	<td><select id="data_status" name="data_status" required="required"
		onchange="show_tz()">
		<option value="final" selected>Final</option>
		<option value="interim">Interim</option>
		<option value="raw">Raw</option>
		<option value="document">Document</option>
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
			echo "<option value='".$row['name']."'";
			//if ($row['name'] == "Europe/Stockholm")
			//	echo " selected";
			echo ">".$row['name'].": ".$row['utc_offset']."</option>";
		}
	}
?>
	</select></td>
	</tr><tr>
	<td>Files:</td>
	<td><input name="file[]" type="file" required="required"
			multiple="multiple"></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Upload</button></td>
	</tr></table>
</form>

<script>
function show_tz () {
	if (document.getElementById("data_status").value == "final") {
		document.getElementById("tzlabel").style.visibility = "visible";
		document.getElementById("tz").style.visibility = "visible";
	} else {
		document.getElementById("tzlabel").style.visibility = "hidden";
		document.getElementById("tz").style.visibility = "hidden";
	}
}
</script>

<?php $db->disconnect(); ?>
