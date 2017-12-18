<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$storage_type=$_POST['storage_type'];
if ($storage_type=="") {
	echo "<p>You must specify storage type.</p>";
	return;
}
$data_subset=$_POST['data_subset'];
if ($data_subset=="") {
	echo "<p>You must specify data subset.</p>";
	return;
}

require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL: select storage name
$sql_storage="
select
	storage_name
from r_storage_type
where storage_type = $1
";

// SQL: select data subset name
$sql_subset="
select
	subset_name
from r_data_subset
where data_subset = $1
";

// SQL: select datasets
$sql_dataset="
select distinct
	d.dataset_id,
	d.title
from p_dataset d, p_column c, p_dataset_role r, r_person p
where d.dataset_id = c.dataset_id
  and d.dataset_id = r.dataset_id
  and r.user_id = p.person_id
  and d.storage_type = $1
  and c.data_subset = $2
  and p.drupal_id = $3
union
select distinct
	d.dataset_id,
	d.title
from p_dataset d, r_data_subset s, p_dataset_role r, r_person p, x_context x
where d.storage_type = s.storage_type
  and d.dataset_id = r.dataset_id
  and r.user_id = p.person_id
  and d.storage_type = $1
  and s.data_subset = $2
  and p.drupal_id = $3
  and x.context_type = 'property'
  and x.context_key = 'static_variables'
  and x.object_type = 'storage_type'
  and x.object_key = d.storage_type
order by title
";

$res = $db->query($sql_storage, array($storage_type));
$storage_name=$res[0]['storage_name'];
$res = $db->query($sql_subset, array($data_subset));
$subset_name=$res[0]['subset_name'];

?>

<p>
Export of data from several datasets with storage type
<?php echo $storage_name; ?>
 and that contains data subsets of type
<?php echo $subset_name; ?>
.
</p>
<p>
Select one or more datasets. You will only find those datasets that you have access to.
</p>

<!-- Form for selection of dataset -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="3" type="hidden" />
	<input name="storage_type" value="<?php echo $storage_type; ?>" type="hidden" />
	<input name="data_subset" value="<?php echo $data_subset; ?>" type="hidden" />
	<table><tr>
	<td>Datasets:</td>
	<td><select id="dataset_arr[]" name="dataset_arr[]" required="required" multiple="multiple" size="10">
		<option value="">Select datasets</option>
<?php
	if ($res = $db->query($sql_dataset, array($storage_type,$data_subset,$user->uid))) {
		foreach ($res as $row)
			echo "<option value='".$row['dataset_id']."'>".
				$row['dataset_id'].", ".$row['title']."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td></td>
	<td><button id="goon" type="submit">Next</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
