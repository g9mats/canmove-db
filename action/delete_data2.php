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
$func_name="delete_data_".$storage_type."_".$data_subset;
require_once $DBRoot."/action/".$func_name.".php";

// SQL statement that deletes all data rows from staging area
$sql_stage="
delete from TABNAME
where dataset_id = $1
";

// SQL: delete variable definitions
$sql_dellvar="
delete from l_column
where dataset_id = $1
  and data_subset = $2
";
$sql_delpvar="
delete from p_column
where dataset_id = $1
  and data_subset = $2
";

// SQL: update file records
$sql_upd="
update l_file set
	registered = false,
	imported = false,
	validated = false,
	loaded = false,
	imported_data = false,
	loaded_data = false
where dataset_id = $1
  and data_subset = $2
  and data_status = 'final'
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Adjust table name in staging area
$sql_stage = str_replace ("TABNAME",
				"l_".$storage_type."_".$data_subset, $sql_stage);

$rc=call_user_func($func_name,$dataset_id);
if ($rc==0) {
	$res=$db->execute($sql_stage,array($dataset_id));
	$res=$db->execute($sql_dellvar, array($dataset_id,$data_subset));
	$res=$db->execute($sql_delpvar, array($dataset_id,$data_subset));
	$res=$db->execute($sql_upd,array($dataset_id,$data_subset));
	echo "Data deleted.<br/>";
} else
	echo "No data deleted.<br/>";
	
?>

<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="" type="hidden" />
	<button type="submit">Delete more data</button>
</form>

<?php $db->disconnect(); ?>
