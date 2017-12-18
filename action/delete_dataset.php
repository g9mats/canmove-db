<?php
// Creator: Mats J Svensson, CAnMove

require_once $DBRoot."/lib/DBLink.php";

$sql="
select
	p.dataset_id,
	p.title
from p_dataset p, p_dataset_role a, r_person r
where p.dataset_id = a.dataset_id
  and a.user_id = r.person_id
  and a.user_role in ('O','A')
  and r.drupal_id = $1
order by p.title
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
Delete a dataset. Select dataset in the listbox.
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
	<td><button type="submit">Delete</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
