<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
require $DBRoot."/lib/get_storage_type.php";
require $DBRoot."/action/list_animals_".$storage_type.".php";
?>
