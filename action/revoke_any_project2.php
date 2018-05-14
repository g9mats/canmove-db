<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$project_id=$_POST['project_id'];
if ($project_id=="") {
	echo "<p>You must specify a project.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

$sql="
select
	r.person_id,
	r.first_name||' '||r.last_name||'(Owner)' as name
from r_person r, p_project_role p
where r.person_id = p.user_id
  and p.project_id = $1
  and p.user_role = 'O'
  and 1 < (
  	select count(*) from p_project_role
	where project_id = $1
	  and user_role = 'O'
	)
union
select
	r.person_id,
	r.first_name||' '||r.last_name||'('||
	case p.user_role
		when 'A' then 'Admin'
		when 'R' then 'Read'
		else p.user_role
	end ||')' as name
from r_person r, p_project_role p
where r.person_id = p.user_id
  and p.project_id = $1
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
	<input name="project_id" value="<?php echo $project_id; ?>" type="hidden" />
	<table><tr>
	<td>User:</td>
	<td><select name="person_id" required="required">
		<option value="" selected>Select user</option>
<?php
	if ($res = $db->query($sql, array($project_id))) {
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
