<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: select current project
$sql_dataset="
select
	p.project_id,
	p.title
from p_dataset d, p_project p
where d.project_id = p.project_id
  and d.dataset_id = $1
";

// SQL: select other projects
$sql_project="
select
	p.project_id,
	p.title
from p_project p, p_project_role a, r_person r
where p.project_id = a.project_id
  and a.user_id = r.person_id
  and a.user_role in ('O','A')
  and r.drupal_id = $1
  and p.project_id <> $2
order by p.title
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$dres = $db->query($sql_dataset, array($dataset_id));
$title = $dres[0]['title'];
$pid = $dres[0]['project_id'];
?>

<p>
Dataset belongs to project "<?php echo $title; ?>".
</p>

<!-- Form for selection of project -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="3" type="hidden" />
	<input name="dataset_id" value="<?php echo $dataset_id; ?>" type="hidden" />
	<table><tr>
	<td>Project:</td>
	<td><select name="project_id" required="required">
		<option value="" selected>Select new project</option>
<?php
	if ($pres = $db->query($sql_project, array($user->uid, $pid))) {
		foreach ($pres as $prow)
			echo "<option value='".$prow['project_id']."'>".
				$prow['title']."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Move</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
