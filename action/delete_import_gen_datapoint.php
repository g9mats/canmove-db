<?php
/*
Creator: Mats J Svensson, CAnMove

This script deletes GEN datapoint data from staging area (l_column and
l_gen_datapoint).
*/

function delete_import_gen_datapoint ($did,$devid,$per,$ver,$vset) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL statement that deletes variables from staging area
$sql_delvar="
delete from l_column
where dataset_id = $1
  and data_subset = 'datapoint'
";

// SQL statement that deletes all data rows from staging area
$sql_delstage="
delete from l_gen_datapoint
where dataset_id = $1
  and device_id = $2
  and period = $3
  and version = $4
  and varset = $5
";

if ($res=$db->execute($sql_delvar, array($did))) {
	if ($res=$db->execute($sql_delstage, array($did,$devid,$per,$ver,$vset))) {
		return 0;
	}
	return 1;
}

}
?>
