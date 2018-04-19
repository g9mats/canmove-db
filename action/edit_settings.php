<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";

// SQL: select person record
$sql_person="
select
	p.person_id,
	p.first_name,
	p.last_name,
	p.drupal_id,
	p.time_zone,
	d.name,
	d.mail
from r_person p, drupal_users d
where p.drupal_id = d.uid
  and p.drupal_id = $1
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

$resP = $db->query($sql_person, array($user->uid));
$rowP = $resP[0];
?>

<p>
</p>

<!-- Form for update of settings -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<input name="person_id" value="<?php echo $rowP['person_id']; ?>" type="hidden" />
	<table><tr>
	<td>First Name:</td>
	<td><?php echo $rowP['first_name'] ?></td>
	</tr><tr>
	<td>Last Name:</td>
	<td><?php echo $rowP['last_name'] ?></td>
	</tr><tr>
	<td>Drupal User:</td>
	<td><?php echo $rowP['name'] ?></td>
	</tr><tr>
	<td>Mail:</td>
	<td><?php echo $rowP['mail'] ?></td>
	</tr><tr>
	<td>Time Zone:</td>
	<td><select name="time_zone">
<?php
	if ($rowP['time_zone'] == "") {
		echo "<option value='' selected>Select time zone</option>";
		echo "<option value='Europe/Stockholm'>Europe/Stockholm</option>";
	} else {
		echo "<option value='".$rowP['time_zone']."'>".$rowP['time_zone']."</option>";
	}
	if ($resTZ = $db->query($sql_tz)) {
		foreach ($resTZ as $rowTZ) {
			echo "<option value='".$rowTZ['name']."'";
			if ($rowTZ['name'] == $rowP['time_zone'])
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
