<?php
/*
Creator: Mats J Svensson, CAnMove

This script deletes GEN trackpoint data from staging area
(l_gen_trackpoint and l_column).
*/

function delete_files_gen_trackpoint ($did,$fid,$devid,$per,$ver) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

require_once $DBRoot."/action/delete_import_gen_trackpoint.php";

// SQL statement that selects all information on a file
$sql_file="
select
	device_id,
	period,
	version,
	imported_data
from l_file
where file_id = $1
";

$res=$db->query($sql_file, array($fid));
$devid=$res[0]['device_id'];
$per=$res[0]['period'];
$ver=$res[0]['version'];
$imported_data=$res[0]['imported_data'];

if ($imported_data)
	return (delete_import_gen_trackpoint($did,$devid,$per,$ver));

return 0;
}
?>
