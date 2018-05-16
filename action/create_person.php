<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";

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
?>

<p>
You must register all persons that need to be referenced as e.g. owners or operators in the database.
If they shall use the database themselfes you must also create an account according to normal Drupal routines and make a reference from the person record.
Only accounts without a reference are shown in the list.
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
		<option value="" selected>Connect to user account</option>
<?php
		if ($res = $db->query($sql_drupal)) {
			foreach ($res as $row)
				echo "<option value='".$row['uid']."'>".$row['name']." - ".$row['mail']."</option>";
		}
?>
	</select></td>
	</tr><tr>
	<td>Time Zone:</td>
	<td><select name="time_zone" required="required">
		<option value="" selected>Select time zone</option>
		<option value="Europe/Stockholm">Europe/Stockholm</option>
<?php
		if ($res = $db->query($sql_tz)) {
			foreach ($res as $row)
				echo "<option value='".$row['name']."'>".$row['name'].": ".$row['utc_offset']."</option>";
		}
?>
	</select></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Save</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
