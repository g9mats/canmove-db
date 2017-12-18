<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";

// SQL: select person record
$sql="
select
	p.person_id,
	p.first_name||' '||p.last_name as name,
	d.name as user
from r_person p left outer join drupal_users d on p.drupal_id = d.uid
where p.person_id > 0
order by p.first_name, p.last_name
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
</p>

<!-- Form for selection of person -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<table><tr>
	<td>User:</td>
	<td><select name="person_id" required="required">
		<option value="" selected>Select person</option>
<?php
	if ($res = $db->query($sql)) {
		foreach ($res as $row) {
			echo "<option value='".$row['person_id']."'>".$row['name'];
			if ($row['user'] != "")
				echo " (".$row['user'].")";
			echo "</option>";
		}
	}
?>
	</select></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Edit</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
