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
	r.person_id,
	r.first_name||' '||r.last_name||'(Owner)' as name
from r_person r, p_dataset_role p
where r.person_id = p.user_id
  and p.dataset_id = $1
  and p.user_role = 'O'
  and 1 < (
  	select count(*) from p_dataset_role
	where dataset_id = $1
	  and user_role = 'O'
	)
union
select
	r.person_id,
	r.first_name||' '||r.last_name||'('||
	case p.user_role
		when 'A' then 'Admin'
		when 'W' then 'Write'
		when 'R' then 'Read'
		else p.user_role
	end ||')' as name
from r_person r, p_dataset_role p
where r.person_id = p.user_id
  and p.dataset_id = $1
  and p.user_role <> 'O'
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
	if ($res = $db->query($sql, array($dataset_id))) {
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
