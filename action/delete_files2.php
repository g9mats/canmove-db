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
$data_status=$_POST['data_status'];
if ($data_status=="") {
	echo "<p>You must specify a data status.</p>";
	return;
}
$file_arr=$_POST['file_arr'];
if ($file_arr[0]=="") {
	echo "<p>You must specify at least one file.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";
require $DBRoot."/lib/get_storage_type.php";
$func_name="delete_files_".$storage_type."_".$data_subset;
require_once $DBRoot."/action/".$func_name.".php";

// SQL: select file record
$sql_file="
select
	original_name,
	archive_name
from l_file
where file_id = $1
";

// SQL: update file record
$sql_upd="
update l_file set
	deleted = true,
	del_time = date_trunc('second',localtimestamp),
	registered = false,
	imported = false,
	validated = false,
	imported_data = false
where file_id = $1
";

$sql_log="
insert into l_file_log (
	file_id,log_action,log_time
) values ($1, 'D', date_trunc('second',localtimestamp))
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$delcount=0;
foreach ($file_arr as $file_id)
	if ($res = $db->query($sql_file,array($file_id))) {
		$row=$res[0];
		$oname=$row['original_name'];
		$path=$DataRoot."/".$storage_type."/".$dataset_id."/".$data_status;
		$aname=$path."/".$row['archive_name'];
		if ($data_status=="final")
			$rc=call_user_func($func_name,$dataset_id,$file_id);
		else
			$rc=0;
		if ($rc==0) {
			if (unlink($aname)) {
				echo $oname.": Deleted.<br/>";
				if ($res = $db->execute($sql_upd,array($file_id))) {
					$delcount++;
					$res=$db->execute($sql_log, array($file_id));
				}
			}
		}
	}

$db->disconnect();
?>

<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="" type="hidden" />
	<button type="submit">Delete more files</button>
</form>

