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
$data_subset=$_POST['data_subset'];
if ($data_subset=="") {
	echo "<p>You must specify a data subset.</p>";
	return;
}
$version=$_POST['version'];
if ($version=="") {
	echo "<p>You must specify a version.</p>";
	return;
}
$remark=$_POST['remark'];
require_once $DBRoot."/lib/DBLink.php";

// SQL: update file record
$sql="
update l_file set
	version = $2,
	registered = true,
	reg_time = date_trunc('second',localtimestamp),
	remark = $3
where file_id = $1
";

$sql_log="
insert into l_file_log (
	file_id,log_action,log_time
) values ($1, 'R', date_trunc('second',localtimestamp))
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

if ($res = $db->execute($sql,
			array($file_id,$version,$remark))) {
	$res=$db->execute($sql_log, array($file_id));
	echo "<p>File registered.</p>";
}
?>

<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<input name="dataset_id" value="<?php echo $dataset_id; ?>" type="hidden" />
	<input name="data_subset" value="<?php echo $data_subset; ?>" type="hidden" />
	<button type="submit">Register more files</button>
</form>

<?php $db->disconnect(); ?>
