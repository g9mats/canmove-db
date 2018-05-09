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
$tz=$_POST['tz'];
if ($tz=="") {
	echo "<p>You must specify a time zone.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
$sql_tz = "set time zone '".$tz."'";
$res = $db->execute($sql_tz);

$sql_device="
select a.animal, d.device
from d_gen_animal a, d_gen_track t, d_gen_device d
where a.animal_id = t.animal_id
  and t.track_id = d.track_id
  and d.device_id = $1
";

$sql_phase="
select m.start_log_time, m.end_log_time, r.description, m.phase_index
from d_gen_migration_phase m, r_migration_phase r
where m.phase_type = r.phase_type
  and m.device_id = $1
  and m.version = $2
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

<table border="1">
<tr>
<th>Start Time</th>
<th>End Time</th>
<th>Phase</th>
<th>Index</th>
</tr>
<?php
if ($res = $db->query($sql_phase, array($device_id,$version)))
	foreach ($res as $row) {
?>
<tr>
<td><?php echo $row['start_log_time']; ?></td>
<td><?php echo $row['end_log_time']; ?></td>
<td><?php echo $row['description']; ?></td>
<td><?php echo $row['phase_index']; ?></td>
</tr>
<?php
}
?>
</table>
<p>
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<input name="dataset_id" value="<?php echo $dataset_id; ?>" type="hidden" />
	<input name="tz" value="<?php echo $tz; ?>" type="hidden" />
	<button type="submit">Select new device</button>
</form>
</p>

<?php $db->disconnect(); ?>
