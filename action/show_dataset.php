<?php
// Creator: Mats J Svensson, CAnMove

require_once $DBRoot."/lib/DBLink.php";

$sql="
select
	d.dataset_id,
	d.title
from p_dataset d, p_dataset_role a, r_person p
where d.dataset_id = a.dataset_id
  and a.user_id = p.person_id
  and p.drupal_id = $1
order by d.title
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
Information on a dataset.
</p>
<p>
Select dataset in the listbox. You will only find those datasets that you have access to.
</p>

<!-- Form for selection of dataset -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<table><tr>
	<td>Dataset:</td>
	<td><select name="dataset_id" required="required">
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
	<td></td>
	<td><button type="submit">Show</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
