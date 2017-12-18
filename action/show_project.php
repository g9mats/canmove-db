<?php
// Creator: Mats J Svensson, CAnMove

require_once $DBRoot."/lib/DBLink.php";

$sql="
select
	pr.project_id,
	pr.title
from p_project pr, p_project_role a, r_person p
where pr.project_id = a.project_id
  and a.user_id = p.person_id
  and p.drupal_id = $1
order by pr.title
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
Information on a project.
</p>
<p>
Select project in the listbox. You will only find those projects that you have access to.
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
	<td><button type="submit">Show</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
