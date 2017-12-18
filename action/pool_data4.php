<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$storage_type=$_POST['storage_type'];
if ($storage_type=="") {
	echo "<p>You must specify a storage type.</p>";
	return;
}
$data_subset=$_POST['data_subset'];
if ($data_subset=="") {
	echo "<p>You must specify a data subset.</p>";
	return;
}
$dataset_arr=json_decode($_POST['dataset_json']);
if (count($dataset_arr)==0) {
	echo "<p>You must specify at least one dataset.</p>";
	return;
}
$var_arr=$_POST['var_arr'];
if (count($var_arr)==0) {
	echo "<p>You must specify at least one variable.</p>";
	return;
}
$var_pos=array_flip($var_arr);
$version=1;

require "./canmove.inc";
require $DBRoot."/action/pool_".strtolower($storage_type)."_".$data_subset.".php";
?>
