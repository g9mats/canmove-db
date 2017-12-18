<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$project_id=$_POST['project_id'];
if ($project_id=="") {
	echo "<p>You must specify a project.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: select project
$sql="
select
	title,
	remark
from p_project
where project_id = $1
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$res = $db->query($sql,array($project_id));
$row = $res[0];
?>

<p></p> 

<!-- Form for creation of project -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="3" type="hidden"/>
	<input name="project_id" value="<?php echo $project_id ?>" type="hidden" />
	<table><tr>
	<td>Title:</td>
	<td><input name="title" required="required" size="66" maxlength="50" value="<?php echo $row['title'] ?>" /></td>
	</tr><tr>
	<td>Remark:</td>
	<td><input name="remark" size="66" value="<?php echo $row['remark']?>" /></td>
	</tr><tr>
	</tr><tr>
	<td></td>
	<td><button type="submit">Save</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
