<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
$device_id=$_POST['device_id'];
if ($device_id=="") {
	echo "<p>You must specify a device.</p>";
	return;
}
$version=$_POST['version'];
if ($version=="") {
	echo "<p>You must specify a version.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
echo "<script>";
require_once $DBRoot."/lib/ctx_phase_index.js";
echo "</script>";
$ajax_file=$WebRoot."/db/action/define_migration_phases_index.php";

$sql_device="
select a.animal, d.device
from d_gen_animal a, d_gen_track t, d_gen_device d
where a.animal_id = t.animal_id
  and t.track_id = d.track_id
  and d.device_id = $1
";

$sql_phase="
select phase_type, description
from r_migration_phase
order by order_no
";

$sql_time="
select p.log_time, p.latitude, p.longitude
from d_gen_animal a, d_gen_track t, d_gen_device d, d_gen_trackpoint p
where a.animal_id = t.animal_id
  and t.track_id = d.track_id
  and d.device_id = p.device_id
  and a.dataset_id = $1
  and d.device_id = $2
except
select p.log_time, p.latitude, p.longitude
from d_gen_animal a, d_gen_track t, d_gen_device d, d_gen_trackpoint p, d_gen_migration_phase m
where a.animal_id = t.animal_id
  and t.track_id = d.track_id
  and d.device_id = p.device_id
  and p.device_id = m.device_id
  and p.log_time between m.start_log_time and m.end_log_time
  and a.dataset_id = $1
  and d.device_id = $2
order by 1
";

if ($res = $db->query($sql_device, array($device_id))) {
	$animal=$res[0]['animal'];
	$device=$res[0]['device'];
}
?>

<p>
<?php echo "Animal Id: ".$animal ?>
<br/>
<?php echo "Device Id: ".$device ?>
<br/>
<?php echo "Version: ".$version ?>
</p>
<p>
Select start and stop times and phase type in the listboxes. Next free index will automatically be calculated.
</p>

<!-- Form for selection of storage type -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="4" type="hidden" />
	<input name="dataset_id" value="<?php echo $dataset_id; ?>" type="hidden" />
	<input id="device_id" name="device_id" value="<?php echo $device_id; ?>" type="hidden" />
	<input id="version" name="version" value="<?php echo $version; ?>" type="hidden" />
	<table><tr>
	<td>Start Time:</td>
	<td><select id="start_log_time" name="start_log_time" required="required">
		<option value="" selected>Select start time</option>
<?php
	if ($res = $db->query($sql_time, array($dataset_id, $device_id))) {
		foreach ($res as $row)
			echo "<option value='".$row['log_time']."'>".
				$row['log_time']."; ".$row['latitude'].", ".$row['longitude']."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td>End Time:</td>
	<td><select id="end_log_time" name="end_log_time" required="required">
		<option value="" selected>Select end time</option>
<?php
	if ($res = $db->query($sql_time, array($dataset_id, $device_id))) {
		foreach ($res as $row)
			echo "<option value='".$row['log_time']."'>".
				$row['log_time']."; ".$row['latitude'].", ".$row['longitude']."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td>Phase:</td>
	<td><select id="phase_type" name="phase_type" required="required"
			onchange="ctx_phase_index('<?php echo $ajax_file; ?>')">
		<option value="" selected>Select phase</option>
<?php
	if ($res = $db->query($sql_phase)) {
		foreach ($res as $row)
			echo "<option value='".$row['phase_type']."'>".
				$row['description']."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td>Index:</td>
	<td><input id="phase_index" name="phase_index" required="required" readonly="readonly" size="3" /></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Save</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
