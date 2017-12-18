<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";

// SQL: select project and role records
$sql="
select
	p.project_id,
	p.title
from p_project p, p_project_role a, r_person r
where p.project_id = a.project_id
  and a.user_id = r.person_id
  and a.user_role in ('O','A')
  and r.drupal_id = $1
  and p.project_id in (
	select project_id
	from p_project_role
	group by project_id
	having count(*) > 1
	)
order by p.title
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
Revoke project role.
</p>
<p>
Any access rights to datasets will be revoked as well.
</p>

<!-- Form for selection of project -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<table><tr>
	<td>Project:</td>
	<td><select name="project_id" required="required">
		<option value="" selected>Select project</option>
<?php
	if ($res = $db->query($sql, array($user->uid))) {
		foreach ($res as $row)
			echo "<option value='".$row['project_id']."'>".
				$row['title']."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Next</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
