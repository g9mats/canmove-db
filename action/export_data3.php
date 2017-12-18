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
$version=$_POST['version'];
$column_arr=$_POST['column_arr'];
if ($data_subset=="datapoint" && $column_arr[0]=="") {
	echo "<p>You must specify at least one column.</p>";
	return;
}

require "./canmove.inc";
require $DBRoot."/lib/get_storage_type.php";
require $DBRoot."/action/export_".$storage_type."_".$data_subset.".php";
?>
