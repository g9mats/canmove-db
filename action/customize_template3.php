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
$var_arr=$_POST['var_arr'];
if (count($var_arr)==0) {
	echo "<p>You must specify at least one variable.</p>";
	return;
}

require "./canmove.inc";
require $DBRoot."/lib/XMLDocument.php";

$xmldoc = new XMLDocument(strtolower($storage_type)."_".$data_subset."template.xml", $storage_type." ".$data_subset);

// Build header row in XML document
$xmldoc->add_row();
foreach ($var_arr as $var)
	$xmldoc->add_cell ($var, 'String');

// display and quit
$xmldoc->display();
?>
