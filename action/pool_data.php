<?php
// Creator: Mats J Svensson, CAnMove

require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
echo "<script>";
require_once $DBRoot."/lib/ctx_storage_uid_subset.js";
echo "</script>";
$ajax_file=$WebRoot."/db/action/pool_storage_subset.php";

// SQL: select storage types
$sql="
select distinct
	s.storage_type,
	s.storage_name
from r_storage_type s, p_dataset d, p_dataset_role a, r_person p
where s.storage_type = d.storage_type
  and d.dataset_id = a.dataset_id
  and a.user_id = p.person_id
  and d.storage_type in (
	select object_key from x_context
	where context_type = 'action'
	  and context_key = 'pool_data'
	  and object_type = 'storage_type'
  )
  and p.drupal_id = $1
order by s.storage_name
";
?>

<p>
Export of data from several datasets into an XML file (can be read by e.g. Excel).
</p>
<p>
You must select storage type before you can select data subset. You will only find those storage types for which there are datasets that you have access to and that are supported by this pooled data export utility.
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
	if ($res = $db->query($sql, array($user->uid))) {
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
