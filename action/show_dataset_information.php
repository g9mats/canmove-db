<?php
// Creator: Mats J Svensson, CAnMove

require_once $DBRoot."/lib/DBLink.php";

$sql="
select
	d.dataset_id,
	d.title,
	s_dataset_owner(d.dataset_id) as owner,
	t.data_name data_name
from p_dataset d, r_data_type t
where d.data_type = t.data_type
order by d.title, t.data_name, owner
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
Information on a dataset.
</p>
<p>
Select dataset in the listbox. You will only find those datasets that are public.
</p>

<!-- Form for selection of dataset -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<table><tr>
	<td>Dataset:</td>
	<td><select name="dataset_id" required="required">
		<option value="" selected>Select dataset</option>
<?php
	if ($res = $db->query($sql)) {
		foreach ($res as $row)
			echo "<option value='".$row['dataset_id']."'>".
				$row['title']." / ".
				$row['data_name']." / ".
				$row['owner']."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Show</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
