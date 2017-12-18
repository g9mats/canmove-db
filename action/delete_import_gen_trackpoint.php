<?php
/*
Creator: Mats J Svensson, CAnMove

This script deletes GEN trackpoint data from staging area (l_column and 
l_gen_trackpoint).
*/

function delete_import_gen_trackpoint ($did,$devid,$per,$ver) {

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
delete from l_gen_trackpoint
where dataset_id = $1
  and device_id = $2
  and period = $3
  and version = $4
";

if ($res=$db->execute($sql_delvar, array($did))) {
	if ($res=$db->execute($sql_delstage, array($did,$devid,$per,$ver))) {
		return 0;
	}
	return 1;
}

}
?>
