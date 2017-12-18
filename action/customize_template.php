<?php
// Creator: Mats J Svensson, CAnMove

require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
echo "<script>";
require_once $DBRoot."/lib/ctx_storage_subset.js";
echo "</script>";
$ajax_file=$WebRoot."/db/action/customize_storage_subset.php";

// SQL: select storage types
$sql="
select distinct
	storage_type,
	storage_name
from r_storage_type
where storage_type in (
	select object_key from x_context
	where context_type = 'action'
	  and context_key = 'customize_template'
	  and object_type = 'storage_type'
  )
order by storage_name
";
?>

<p>
Create customized template with exactly the variables you want to use. The result will be an XML file (can be read by e.g. Excel).
</p>
<p>
You must select storage type before you can select data subset. You will only find those storage types that are supported by this utility.
</p>

<!-- Form for selection of dataset -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<input id="uid" name="uid" value="<?php echo $user->uid; ?>" type="hidden" />
	<table><tr>
	<td>Storage Type:</td>
	<td><select id="storage_type" name="storage_type" required="required"
			onchange="ctx_storage_subset('<?php echo $ajax_file; ?>')">
		<option value="" selected>Select storage type</option>
<?php
	if ($res = $db->query($sql, array())) {
		foreach ($res as $row)
			echo "<option value='".$row['storage_type']."'>".
				$row['storage_name']."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td>Data Subset:</td>
	<td><select id="data_subset" name="data_subset" required="required">
	</select></td>
	</tr><tr>
	<td></td>
	<td><button id="goon" type="submit">Next</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
