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
	p.time_zone,
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
where uid > 1
except
select
	d.uid,
	d.name,
	d.mail
from drupal_users d, r_person p
where d.uid = p.drupal_id
order by name
";

$sql_tz="
select name,utc_offset
from pg_timezone_names
where name like '%/%'
  and name not like 'Etc%'
  and name not like 'posix%'
  and name not like '%UTC%'
  and name not like '%UCT%'
order by name
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$resP = $db->query($sql_person, array($person_id));
$rowP = $resP[0];
$drupal_id = $rowP['drupal_id'];
$time_zone = $rowP['time_zone'];
?>

<p>
You must register all persons that need to be referenced as e.g. owners or operators in the database.
<br/>
If they shall use the database themselfes you must also create a Drupal account according to normal routines and make a reference from the person record here.
Only accounts without an active reference are shown in the account list below.
<br/>
After a Drupal account has been created it takes to the next day before it is visible on this page.
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
		echo "<option value='' selected>Connect to user account</option>";
	else {
		echo "<option value='".$rowP['drupal_id']."' selected>".
			$rowP['name']." - ".$rowP['mail']."</option>";
		echo "<option value=''>Disconnect from user account</option>";
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
	<td>Time Zone:</td>
	<td><select name="time_zone" required="required">
<?php
	if ($time_zone == "") {
		echo "<option value='' selected>Select time zone</option>";
		echo "<option value='Europe/Stockholm'>Europe/Stockholm</option>";
	} else {
		echo "<option value='".$time_zone."'>".$time_zone."</option>";
		echo "<option value=''>Erase time zone</option>";
	}
	if ($resTZ = $db->query($sql_tz)) {
		foreach ($resTZ as $rowTZ) {
			echo "<option value='".$rowTZ['name']."'";
			if ($rowTZ['name'] == $time_zone)
				echo " selected";
			echo ">".$rowTZ['name'].": ".$rowTZ['utc_offset']."</option>";
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
