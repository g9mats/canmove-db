<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$find=$_POST['find'];
require_once $DBRoot."/lib/DBLink.php";

// SQL: select person records
$sql="
select
	first_name,
	last_name,
	person_id
from r_person
where person_id > 0
  and upper(first_name||' '||last_name||' '||person_id) like upper('%'||$1||'%')
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
<th>Operator Id</th>
</tr>

<?php // For every person
if ($res = $db->query($sql,array($find)))
	foreach ($res as $row) {
?>
<tr>
<td><?php echo $row['first_name']; ?></td>
<td><?php echo $row['last_name']; ?></td>
<td><?php echo $row['person_id']; ?></td>
</tr>
<?php
	}
?>

</table>

<?php $db->disconnect(); ?>
