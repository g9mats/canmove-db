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

$sql="
select distinct a.animal, d.device_id, d.device, p.version
from d_gen_animal a, d_gen_track t, d_gen_device d, d_gen_trackpoint p
where a.animal_id = t.animal_id
  and t.track_id = d.track_id
  and d.device_id = p.device_id
  and a.dataset_id = $1
  and exists (
	select 1
	from d_gen_migration_phase m
	where m.device_id = d.device_id
	)
order by a.animal, d.device, p.version
";
?>

<p>
Select a device in the listbox.
</p>

<!-- Form for selection of storage type -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="3" type="hidden" />
	<input name="dataset_id" value="<?php echo $dataset_id; ?>" type="hidden" />
	<input id="device_id" name="device_id" value="" type="hidden" />
	<input id="version" name="version" value="" type="hidden" />
	<table><tr>
	<td>Device:</td>
	<td><select id="dev_ver" name="dev_ver" required="required" onchange="get_keys()">
		<option value="" selected>Select device</option>
<?php
	if ($res = $db->query($sql, array($dataset_id))) {
		foreach ($res as $row)
			echo "<option value='".$row['device_id']."/".$row['version']."'>".
				$row['animal']." - ".$row['device'].";".$row['version'].
				"</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Next</button></td>
	</tr></table>
</form>

<script>
function get_keys() {
	var keys=document.getElementById("dev_ver").value;
	var idx=keys.indexOf("/");

	if (idx == -1) {
		document.getElementById("device_id").value="";
		document.getElementById("version").value="";
	} else {
		document.getElementById("device_id").value=keys.substr(0,idx);
		document.getElementById("version").value=keys.substr(idx+1);
	}
}
</script>

<?php $db->disconnect(); ?>
