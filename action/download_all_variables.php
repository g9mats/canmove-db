<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";

// SQL: select storage types
$sql="
select
	x.object_key,
	r.storage_name
from x_context x, r_storage_type r
where x.object_key = r.storage_type
  and context_type = 'action'
  and context_key = 'list_all_variables'
  and object_type = 'storage_type'
order by r.storage_name
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>


<p>
List of valid variables and the headers that should be used in the spreadsheets.
</p>
<p>
Select storage type in the listbox.
</p>

<!-- Form for selection of storage type -->
<form action="/db/action/download_all_variables2.php" method="post">
	<table><tr>
	<td>Storage Type:</td>
	<td><select name="storage_type" required="required">
<?php
		if ($res = $db->query($sql)) {
			foreach ($res as $row)
				echo "<option value='".$row['object_key']."'>".$row['storage_name']."</option>";
		}
?>
	</select></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Download</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
