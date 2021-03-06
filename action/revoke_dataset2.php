<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

$sql="
select
	p.person_id,
	p.first_name||' '||p.last_name||'('||
	case a.user_role
		when 'O' then 'Owner'
		when 'A' then 'Admin'
		when 'W' then 'Write'
		when 'R' then 'Read'
		else a.user_role
	end ||')' as name
from p_dataset_role a, r_person p
where a.user_id = p.person_id
  and a.dataset_id = $1
  and case when p.drupal_id is null then -1 else p.drupal_id end <> $2
order by name
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
Select user in the listbox.
</p>

<!-- Form for selection of user and role -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="3" type="hidden" />
	<input name="dataset_id" value="<?php echo $dataset_id; ?>" type="hidden" />
	<table><tr>
	<td>User:</td>
	<td><select name="person_id" required="required">
		<option value="" selected>Select user</option>
<?php
	if ($res = $db->query($sql, array($dataset_id,$user->uid))) {
		foreach ($res as $row)
			echo "<option value='".$row['person_id']."'>".
				$row['name']."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Revoke</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
