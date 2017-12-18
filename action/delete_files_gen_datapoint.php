<?php
/*
Creator: Mats J Svensson, CAnMove

This script deletes GEN datapoint data from staging area
(l_gen_datapoint and l_column).
*/

function delete_files_gen_datapoint ($did,$fid) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

require_once $DBRoot."/action/delete_import_gen_datapoint.php";

// SQL statement that selects all information on a file
$sql_file="
select
	device_id,
	period,
	version,
	varset,
	imported_data
from l_file
where file_id = $1
";

$res=$db->query($sql_file, array($fid));
$devid=$res[0]['device_id'];
$per=$res[0]['period'];
$ver=$res[0]['version'];
$vset=$res[0]['varset'];
$imported_data=$res[0]['imported_data'];

if ($imported_data)
	return (delete_import_gen_datapoint($did,$devid,$per,$ver,$vset));

return 0;
}
?>
