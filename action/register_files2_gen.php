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
if (!isset($period)) $period="-";
if (!isset($version)) $version=1;
if (!isset($varset)) $varset="-";
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
echo "<script>";
require_once $DBRoot."/lib/ctx_file_info_gen.js";
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

// SQL: select GEN device records
$sql_device="
select
	d.device_id,
	a.animal||' - '||d.device||' - '||c.capture_time as device_text
from d_gen_animal a, d_gen_track t, d_gen_device d, d_gen_capture c
where a.animal_id = t.animal_id
  and t.track_id = d.track_id
  and t.start_capture_id = c.capture_id
  and dataset_id = $1
order by a.animal, d.device
";

// SQL: select versions
$sql_ver="
select max(version) as maxver
from l_file
where dataset_id = $1
";
?>

<p>
Select file and device in the listboxes.
</p>

<!-- Form for selection of file -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="3" type="hidden" />
	<input name="dataset_id" value="<?php echo $dataset_id; ?>" type="hidden" />
	<input name="data_subset" value="<?php echo $data_subset; ?>" type="hidden" />
	<table><tr>
	<td>Files:</td>
	<td><select id="file_id" name="file_id" required="required"
			onchange="ctx_file_info_gen('<?php echo $ajax_file; ?>')">
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
	<td>Device:</td>
	<td><select id="device_id" name="device_id" required="required" size="10">
<?php
	if ($res = $db->query($sql_device, array($dataset_id))) {
		foreach ($res as $row)
			echo "<option value='".$row['device_id']."'>".
				$row['device_text']."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td>Period:</td>
	<td><input id="period" name="period" value="<?php echo $period; ?>" /></td>
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
	<td id="vslabel">Varible Set:</td>
	<td><input id="varset" name="varset" value="<?php echo $varset; ?>" /></td>
	</tr><tr>
	<td>Remark:</td>
	<td><input id="remark" name="remark" size="75" /></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Register</button></td>
	</tr></table>
</form>

<script>
(function(){
	if ("<?php echo $data_subset; ?>" == "datapoint") {
		document.getElementById("vslabel").style.visibility = "visible";
		document.getElementById("varset").style.visibility = "visible";
	} else {
		document.getElementById("vslabel").style.visibility = "hidden";
		document.getElementById("varset").style.visibility = "hidden";
	}
})()
</script>

<?php $db->disconnect(); ?>
