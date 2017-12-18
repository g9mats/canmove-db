<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$context_type=$_POST['context_type'];
$context_key=$_POST['context_key'];
$object_type=$_POST['object_type'];
$object_key=$_POST['object_key'];
$object_key2=$_POST['object_key2'];

require_once $DBRoot."/lib/DBLink.php";

// SQL: select context records
$sql="
select
	context_type,
	context_key,
	object_type,
	object_key,
	object_key2,
	order_no
from x_context
where context_type like $1
  and context_key like $2
  and object_type like $3
  and object_key like $4
  and object_key2 like $5
order by context_type, context_key, object_type, order_no,
	object_key, object_key2
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<table border="1">
<tr>
<th>Context Type</th>
<th>Context Key</th>
<th>Object Type</th>
<th>Object Key</th>
<th>Object Key 2</th>
<th>Order No</th>
</tr>
<?php
// Get context information and present it in a table
$i=0;
if ($res = $db->query($sql,
		array(
			"%".$context_type."%",
			"%".$context_key."%",
			"%".$object_type."%",
			"%".$object_key."%",
			"%".$object_key2."%"
			)))
	foreach ($res as $row) {
?>
<tr>
<td><?php echo $row['context_type']; ?></td>
<td><?php echo $row['context_key']; ?></td>
<td><?php echo $row['object_type']; ?></td>
<td><?php echo $row['object_key']; ?></td>
<td><?php echo $row['object_key2']; ?></td>
<td><?php echo $row['order_no']; ?></td>
</tr>
<?php
	$i++;
	}
?>
</table>

<?php
echo "Rows: ".$i;
$db->disconnect();
?>
