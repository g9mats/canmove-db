<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
$device_id=$_POST['device_id'];
$version=$_POST['version'];
$phase_type=$_POST['phase_type'];
if ($phase_type=="") {
	echo "<p>You must specify a phase type.</p>";
	return;
}
$phase_index=$_POST['phase_index'];
if ($phase_index=="") {
	echo "<p>You must specify a phase index.</p>";
	return;
}
$start_log_time=$_POST['start_log_time'];
if ($start_log_time=="") {
	echo "<p>You must specify a start log time.</p>";
	return;
}
$end_log_time=$_POST['end_log_time'];
if ($end_log_time=="") {
	echo "<p>You must specify an end log time.</p>";
	return;
}
if ($start_log_time>$end_log_time) {
	echo "<p>Start time must be less than or equal to end time.</p>";
	echo "<button onclick='history.go(-1)'>Try again</button>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: update person record
$sql="
insert into d_gen_migration_phase (
	device_id,
	version,
	start_log_time,
	end_log_time,
	phase_type,
	phase_index
	) values ($1, $2, $3, $4, $5, $6)
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Update person
if ($res = $db->execute($sql, array(
		$device_id,
		$version,
		$start_log_time,
		$end_log_time,
		$phase_type,
		$phase_index))) {
	echo "<p>Migration phase defined.</p>";
}
?>

<p>
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="3" type="hidden" />
	<input name="dataset_id" value="<?php echo $dataset_id; ?>" type="hidden" />
	<input name="device_id" value="<?php echo $device_id; ?>" type="hidden" />
	<input name="version" value="<?php echo $version; ?>" type="hidden" />
	<button type="submit">Define more phases</button>
</form>
</p>

<p>
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<input name="dataset_id" value="<?php echo $dataset_id; ?>" type="hidden" />
	<button type="submit">Select new device</button>
</form>
</p>

<?php $db->disconnect(); ?>
