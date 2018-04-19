<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";

// SQL: select person records
$sql="
select
	first_name,
	last_name,
	person_id,
	drupal_id,
	time_zone
from r_person
where person_id > 0
  and drupal_id is not null
order by first_name, last_name
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
</p>

<table>
<tr>
<th>First Name</th>
<th>Last Name</th>
<th>Person Id</th>
<th>Drupal Id</th>
<th>Time Zone</th>
</tr>

<?php // For every person
if ($res = $db->query($sql))
	foreach ($res as $row) {
?>
<tr>
<td><?php echo $row['first_name']; ?></td>
<td><?php echo $row['last_name']; ?></td>
<td><?php echo $row['person_id']; ?></td>
<td><?php echo $row['drupal_id']; ?></td>
<td><?php echo $row['time_zone']; ?></td>
</tr>
<?php
	}
?>

</table>

<?php $db->disconnect(); ?>
