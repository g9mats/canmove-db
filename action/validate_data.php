<?php
// Creator: Mats J Svensson, CAnMove

require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
echo "<script>";
require_once $DBRoot."/lib/ctx_dataset_subset.js";
echo "</script>";
$ajax_file=$WebRoot."/db/action/validate_data_subset.php";

$sql="
select distinct
	d.dataset_id,
	d.title
from p_dataset d, p_dataset_role a, r_person p, l_file f
where d.dataset_id = a.dataset_id
  and a.user_id = p.person_id
  and d.dataset_id = f.dataset_id
  and d.storage_type in (
	select object_key from x_context
	where context_type = 'action'
	  and context_key = 'validate_data'
	  and object_type = 'storage_type'
  )
  and a.user_role in ('O','A','W')
  and f.data_status = 'final'
  and f.imported
  and not f.validated
  and p.drupal_id = $1
order by d.title
";
?>

<p>
Validate data in temporary database storage.
</p>
<p>
You must select dataset before you can select data subset. You will only find those datasets that you have write access to and for which there is data in temporary storage not yet validated with a data type that is supported by this validation utility.
</p>

<!-- Form for selection of dataset -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<table><tr>
	<td>Dataset:</td>
	<td><select id="dataset_id" name="dataset_id" required="required"
			onchange="ctx_dataset_subset('<?php echo $ajax_file; ?>')">
		<option value="" selected>Select dataset</option>
<?php
	if ($res = $db->query($sql, array($user->uid))) {
		foreach ($res as $row)
			echo "<option value='".$row['dataset_id']."'>".
				$row['title']."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td>Data Subset:</td>
	<td><select id="data_subset" name="data_subset" required="required">
	</select></td>
	</tr><tr>
	<td></td>
	<td>Please be patient while data is validated.</td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Validate</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
