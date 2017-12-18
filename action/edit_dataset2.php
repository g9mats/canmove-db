<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: select dataset
$sql_dataset="
select
	d.title,
	d.data_type,
	r.data_name,
	d.storage_type,
	s.storage_name,
	d.method,
	d.site,
	d.start_date,
	d.end_date,
	d.animal_num,
	d.track_num,
	d.animal_db,
	d.track_db,
	d.data_location,
	d.contact_id,
	d.public,
	d.release_date,
	d.remark,
	p.first_name||' '||p.last_name as name
from p_dataset d left outer join r_person p
	on d.contact_id = p.person_id, r_data_type r, r_storage_type s
where d.dataset_id = $1
  and d.data_type = r.data_type
  and d.storage_type = s.storage_type
";

// SQL: select data types
$sql_data_type="
select
	data_type,
	data_name
from r_data_type
where storage_type = $1
  and data_type <> $2
order by data_name
";

// SQL: select persons
$sql_person="
select
	person_id,
	first_name||' '||last_name as name
from r_person
order by name
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$res = $db->query($sql_dataset,array($dataset_id));
$row = $res[0];
$data_type = $row['data_type'];
$storage_type = $row['storage_type'];
$contact_id = $row['contact_id'];

?>

<p></p> 

<!-- Form for update of dataset -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="3" type="hidden"/>
	<input name="dataset_id" value="<?php echo $dataset_id ?>" type="hidden" />
	<table><tr>
	<td>Title:</td>
	<td colspan="3"><input name="title" required="required" size="66" maxlength="50" value="<?php echo $row['title'] ?>" /></td>
	</tr><tr>
	<td>Data Type:</td>
	<td colspan="3"><select name="data_type">
<?php
	echo "<option value='".$data_type."' selected>".
		$row['data_name']."</option>";
	if ($resD = $db->query($sql_data_type,array($storage_type,$data_type))) {
		foreach ($resD as $rowD) {
			echo "<option value='".$rowD['data_type']."'>".
				$rowD['data_name']."</option>";
		}
	}
?>
	</select></td>
	</tr><tr>
	<td>Storage Type:</td>
	<td colspan="3"><?php echo $row['storage_name'] ?></td>
	</tr><tr>
	<td>Method:</td>
	<td colspan="3"><input name="method" size="66" maxlength="100" value="<?php echo $row['method'] ?>" /></td>
	</tr><tr>
	<td>Site:</td>
	<td colspan="3"><input name="site" size="66" maxlength="100" value="<?php echo $row['site'] ?>" /></td>
	</tr><tr>
	<td><br/></td>
	</tr><tr>
	<td>Start Date:</td>
	<td><input name="start_date" size="10" value="<?php echo $row['start_date'] ?>" /></td>
	<td>End Date:</td>
	<td><input name="end_date" size="10" value="<?php echo $row['end_date'] ?>" /></td>
	</tr><tr>
	<td>Number of Animals:</td>
	<td><input name="animal_num" size="10" required="required" value="<?php echo $row['animal_num'] ?>" /></td>
	<td>Animals in Database:</td>
	<td><input name="animal_db" size="10" required="required" value="<?php echo $row['animal_db'] ?>" /></td>
	</tr><tr>
	<td>Number of Tracks:</td>
	<td><input name="track_num" size="10" required="required" value="<?php echo $row['track_num'] ?>" /></td>
	<td>Tracks in Database:</td>
	<td><input name="track_db" size="10" required="required" value="<?php echo $row['track_db'] ?>" /></td>
	</tr><tr>
	<td><br/></td>
	</tr><tr>
	<td>Data Location:</td>
	<td><input name="data_location" size="25" maxlength="20" value="<?php echo $row['data_location'] ?>" /></td>
	</tr><tr>
	<td>Contact Person:</td>
	<td><select name="contact_id">
<?php
	if ($contact_id == "")
		echo "<option value='' selected>Select contact person</option>";
	else {
		echo "<option value='".$contact_id."' selected>".
			$row['name']."</option>";
		echo "<option value=''>Erase contact person</option>";
		}
	if ($resP = $db->query($sql_person)) {
		foreach ($resP as $rowP) {
			echo "<option value='".$rowP['person_id']."'>".
				$rowP['name']."</option>";
		}
	}
?>
	</select></td>
	</tr><tr>
	<td>Public:</td>
	<td><select id="public" name="public" required="required">
		<option value="false">No</option>
		<option value="true">Yes</option>
	</select></td>
	<td>Release Date:</td>
	<td><input name="release_date" size="10" value="<?php echo $row['release_date'] ?>" /></td>
	</tr><tr>
	<td><br/></td>
	</tr><tr>
	<td>Remark:</td>
	<td colspan="3"><input name="remark" size="66" value="<?php echo $row['remark']?>" /></td>
	</tr><tr>
	</tr><tr>
	<td></td>
	<td><button type="submit">Save</button></td>
	</tr></table>
</form>

<script>
(function(){
	document.getElementById("public").value = "<?php if ($row['public']=="t") echo "true"; else echo "false"; ?>";
})()
</script>

<?php $db->disconnect(); ?>
