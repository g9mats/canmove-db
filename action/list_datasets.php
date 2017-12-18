<?php
// Creator: Mats J Svensson, CAnMove

require_once $DBRoot."/lib/DBLink.php";

$sql="
select
	p.project_id,
	p.title
from p_project p, p_project_role a, r_person r
where p.project_id = a.project_id
  and a.user_id = r.person_id
  and r.drupal_id = $1
order by p.title
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
List datasets. Select project in the listbox or leave it blank.
</p>

<!-- Form for selection of project -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<table><tr>
	<td>Project:</td>
	<td><select name="project_id">
		<option value="" selected>All projects</option>
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
