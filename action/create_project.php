<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";

$sql="
select
	person_id,
	first_name||' '|| last_name as name
from r_person
order by first_name, last_name
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
Create a new project.
</p>

<!-- Form for creation of project -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<table><tr>
	<td>Title:</td>
	<td><input name="title" required="required" size="66" maxlength="50" /></td>
	</tr><tr>
	<td>Owner:</td>
	<td><select name="owner_id" required="required">
		<option value="" selected>Select owner</option>
<?php
	if ($res = $db->query($sql)) {
		foreach ($res as $row)
			echo "<option value='".$row['person_id']."'>".
				$row['name']."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td>Remark:</td>
	<td><input name="remark" size="66" /></td>
	</tr><tr>
	</tr><tr>
	<td></td>
	<td><button type="submit">Create</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
