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
require_once $DBRoot."/lib/DBLink.php";
require $DBRoot."/lib/get_storage_type.php";
$func_name="load_".$storage_type."_".$data_subset;
require_once $DBRoot."/action/".$func_name.".php";

// SQL: select file records
$sql_file="
select
	file_id
from l_file f
where dataset_id = $1
  and data_subset = $2
  and data_status = 'final'
  and validated
  and not loaded
order by original_name,device_id,period,version,varset
";

// SQL: update file record
$sql_upd="
update l_file set
	loaded = true,
	load_time = date_trunc('second',localtimestamp),
	imported_data = false,
	loaded_data = true
where file_id = $1
";

$sql_log="
insert into l_file_log (
	file_id,log_action,log_time
) values ($1, 'L', date_trunc('second',localtimestamp))
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$count=0;
$res=$db->query($sql_file,array($dataset_id,$data_subset));
foreach ($res as $row) {
	$file_id=$row['file_id'];
	$rc=call_user_func($func_name,$dataset_id,$file_id);
	if ($rc==0)
		if ($res = $db->execute($sql_upd,array($file_id))) {
			$count++;
			$res=$db->execute($sql_log, array($file_id));
		}
}
if ($count==1)
	echo "Data from ".$count." ".$data_subset." file loaded.<br/>";
else
	echo "Data from ".$count." ".$data_subset." files loaded.<br/>";
?>

<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="" type="hidden" />
	<button type="submit">Load more data</button>
</form>

<?php $db->disconnect(); ?>
