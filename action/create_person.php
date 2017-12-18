<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";

// SQL: select drupal user records
$sql="
select
	uid,
	name,
	mail
from drupal_users
where uid > 0
except
select
	d.uid,
	d.name,
	d.mail
from drupal_users d, r_person p
where d.uid = p.drupal_id
order by name
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
</p>

<!-- Form for insert of person -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<table><tr>
	<td>First Name:</td>
	<td><input name="first_name" required="required" value="" /></td>
	</tr><tr>
	<td>Last Name:</td>
	<td><input name="last_name" required="required" value="" /></td>
	</tr><tr>
	<td>Drupal User:</td>
	<td><select name="drupal_id">
		<option value="" selected>Connect to user</option>
<?php
		if ($res = $db->query($sql)) {
			foreach ($res as $row)
				echo "<option value='".$row['uid']."'>".$row['name']." - ".$row['mail']."</option>";
		}
?>
	</select></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Save</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
