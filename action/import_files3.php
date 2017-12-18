<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
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
$file_arr=$_POST['file_arr'];
if (count($file_arr)==1 && $file_arr[0]=="") {
	echo "<p>You must specify at least one file.</p>";
	return;
}
$separator=$_POST['separator'];
if ($separator=="") {
	echo "<p>You must specify a separator.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";
require $DBRoot."/lib/get_storage_type.php";
$func_name="import_".$storage_type."_".$data_subset;
require_once $DBRoot."/action/".$func_name.".php";

// SQL: select file record
$sql_name="
select
	original_name,
	archive_name
from l_file
where file_id = $1
";

// SQL: update file record
$sql_upd="
update l_file set
	imported = true,
	imp_time = date_trunc('second',localtimestamp),
	imported_data = true
where file_id = $1
";

$sql_log="
insert into l_file_log (
	file_id,log_action,log_time
) values ($1, 'I', date_trunc('second',localtimestamp))
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$count=0;
foreach ($file_arr as $file_id) {
	if ($file_id=="") continue;
	if ($res = $db->query($sql_name,array($file_id))) {
		$row=$res[0];
		$file_name=$DataRoot."/".$storage_type."/".$dataset_id."/final/".$row['archive_name'];
		$rc=call_user_func($func_name,$file_name,$separator,$file_id);
		if ($rc==0)
			if ($res = $db->execute($sql_upd,array($file_id))) {
				$count++;
				$res=$db->execute($sql_log, array($file_id));
			}
	}
}
if ($count==1)
	echo "1 file imported.<br/>";
else
	echo $count." files imported.<br/>";
?>

<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="" type="hidden" />
	<input name="dataset_id" value="<?php echo $dataset_id; ?>" type="hidden" />
	<input name="data_subset" value="<?php echo $data_subset; ?>" type="hidden" />
	<button type="submit">Import more files</button>
</form>

<?php $db->disconnect(); ?>
