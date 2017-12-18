<?php
/*
Creator: Mats J Svensson, CAnMove

This script deletes NBL trackpoint data from staging area (l_column and
l_nbl_trackpoint).
*/

function delete_import_nbl_trackpoint ($did) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL statement that deletes variables from staging area
$sql_delvar="
delete from l_column
where dataset_id = $1
  and data_subset = 'trackpoint'
";

// SQL statement that deletes all data rows from staging area
$sql_delstage="
delete from l_nbl_trackpoint
where dataset_id = $1
";

if ($res=$db->execute($sql_delvar, array($did))) {
	if ($res=$db->execute($sql_delstage, array($did))) {
		return 0;
	}
	return 1;
}

}
?>
