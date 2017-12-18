<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";

// SQL: select dataset and role records
$sql="
select
	d.dataset_id,
	d.title
from p_dataset d, p_dataset_role a, r_person r
where d.dataset_id = a.dataset_id
  and a.user_id = r.person_id
  and a.user_role in ('O','A')
  and r.drupal_id = $1
  and d.dataset_id in (
	select d.dataset_id
	from p_dataset d, p_project_role p
	where d.project_id = p.project_id
	group by d.dataset_id
	having count(*) > 1
	)
order by d.title
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
Grant dataset role.
</p>
<p>
You can only grant dataset access to users with project access.
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
	<td><button type="submit">Next</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
