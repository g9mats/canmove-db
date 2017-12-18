<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$project_id=$_POST['project_id'];
if ($project_id=="") {
	echo "<p>You must specify a project.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: select data types
$sql_data_type="
select
	data_type,
	data_name
from r_data_type
order by data_name
";

// SQL: select possible owners
$sql_owner="
select
	p.person_id,
	p.first_name||' '||p.last_name as name
from r_person p, p_project_role r
where p.person_id = r.user_id
  and r.project_id = $1
order by p.first_name, p.last_name
";

// SQL: select persons
$sql_person="
select
	person_id,
	first_name||' '||last_name as name
from r_person
order by first_name, last_name
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
Create a new dataset.
</p>

<!-- Form for creation of dataset -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="3" type="hidden" />
	<input name="project_id" value="<?php echo $project_id; ?>" type="hidden" />
	<table><tr>
	<td>Title:</td>
	<td colspan="3"><input name="title" required="required" size="66" maxlength="50" /></td>
	</tr><tr>
	<td>Owner:</td>
	<td><select name="owner_id" required="required">
		<option value="" selected>Select owner</option>
<?php
	if ($res = $db->query($sql_owner, array($project_id))) {
		foreach ($res as $row)
			echo "<option value='".$row['person_id']."'>".
				$row['name']."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td>Data Type:</td>
	<td><select name="data_type" required="required">
		<option value="" selected>Select data type</option>
<?php
	if ($res = $db->query($sql_data_type)) {
		foreach ($res as $row)
			echo "<option value='".$row['data_type']."'>".
				$row['data_name']."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td>Method:</td>
	<td colspan="3"><input name="method" size="66" maxlength="100" /></td>
	</tr><tr>
	<td>Site:</td>
	<td colspan="3"><input name="site" size="66" maxlength="100" /></td>
	</tr><tr>
	<td><br/></td>
	</tr><tr>
	<td>Start Date:</td>
	<td><input name="start_date" size="10" /></td>
	<td>End Date:</td>
	<td><input name="end_date" size="10" /></td>
	</tr><tr>
	<td>Number of Animals:</td>
	<td><input name="animal_num" size="10" required="required" value="0" /></td>
	<td>Animals in Database:</td>
	<td><input name="animal_db" size="10" required="required" value="0" /></td>
	</tr><tr>
	<td>Number of Tracks:</td>
	<td><input name="track_num" size="10" required="required" value="0" /></td>
	<td>Tracks in Database:</td>
	<td><input name="track_db" size="10" required="required" value="0" /></td>
	</tr><tr>
	<td><br/></td>
	</tr><tr>
	<td>Data Location:</td>
	<td><input name="data_location" size="25" maxlength="20" /></td>
	</tr><tr>
	<td>Contact Person:</td>
	<td><select name="contact_id">
		<option value="" selected>Select contact person</option>
<?php
	if ($res = $db->query($sql_person)) {
		foreach ($res as $row)
			echo "<option value='".$row['person_id']."'>".
				$row['name']."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td>Public:</td>
	<td><select name="public" required="required">
		<option value="false" selected>No</option>
		<option value="true">Yes</option>
	</select></td>
	<td>Release Date:</td>
	<td><input name="release_date" size="10" /></td>
	</tr><tr>
	<td><br/></td>
	</tr><tr>
	<td>Remark:</td>
	<td colspan="3"><input name="remark" size="66" /></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Create</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
