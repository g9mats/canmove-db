<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$person_id=$_POST['person_id'];
if ($person_id=="") {
	echo "<p>You must specify a person.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: select person record
$sql_person="
select
	p.first_name,
	p.last_name,
	p.drupal_id,
	d.name,
	d.mail
from r_person p left outer join drupal_users d
	on p.drupal_id = d.uid
where p.person_id = $1
";

// SQL: select drupal user records
$sql_drupal="
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

$resP = $db->query($sql_person, array($person_id));
$rowP = $resP[0];
$drupal_id = $rowP['drupal_id'];
?>

<p>
</p>

<!-- Form for update of person -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="3" type="hidden" />
	<input name="person_id" value="<?php echo $person_id; ?>" type="hidden" />
	<table><tr>
	<td>First Name:</td>
	<td><input name="first_name" required="required"
		value="<?php echo $rowP['first_name'] ?>" /></td>
	</tr><tr>
	<td>Last Name:</td>
	<td><input name="last_name" required="required"
		value="<?php echo $rowP['last_name'] ?>" /></td>
	</tr><tr>
	<td>Drupal User:</td>
	<td><select name="drupal_id">
<?php
	if ($drupal_id == "")
		echo "<option value='' selected>Connect to user</option>";
	else {
		echo "<option value='".$rowP['drupal_id']."' selected>".
			$rowP['name']." - ".$rowP['mail']."</option>";
		echo "<option value=''>Disconnect from user</option>";
		}
	if ($resD = $db->query($sql_drupal)) {
		foreach ($resD as $rowD) {
			echo "<option value='".$rowD['uid']."'";
			if ($rowD['uid'] == $drupal_id)
				echo " selected";
			echo ">".$rowD['name']." - ".$rowD['mail']."</option>";
		}
	}
?>
	</select></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Save</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
