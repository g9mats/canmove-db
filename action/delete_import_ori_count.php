<?php
/*
Creator: Mats J Svensson, CAnMove

This script deletes ORI count data from staging area (l_column and 
l_ori_count).
*/

function delete_import_ori_count ($did,$ver) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL statement that deletes variables from staging area
$sql_delvar="
delete from l_column
where dataset_id = $1
  and data_subset = 'count'
";

// SQL statement that deletes all data rows from staging area
$sql_delstage="
delete from l_ori_count
where dataset_id = $1
  and version = $2
";

if ($res=$db->execute($sql_delvar, array($did))) {
	if ($res=$db->execute($sql_delstage, array($did,$ver))) {
		return 0;
	}
	return 1;
}

}
?>
