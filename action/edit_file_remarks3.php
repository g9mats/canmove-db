<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$file_id=$_POST['file_id'];
if ($file_id=="") {
	echo "<p>You must specify a file.</p>";
	return;
}
$dataset_id=$_POST['dataset_id'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
$remark=$_POST['remark'];
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL: update file record
$sql="
update l_file set
	remark = $2
where file_id = $1
";

if ($res = $db->execute($sql,array($file_id,$remark))) {
	echo "<p>File remark saved.</p>";
}
?>

<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<input name="dataset_id" value="<?php echo $dataset_id; ?>" type="hidden" />
	<button type="submit">Edit more file remarks</button>
</form>

<?php $db->disconnect(); ?>
