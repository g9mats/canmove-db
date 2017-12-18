<?php
/*
Creator: Mats J Svensson, CAnMove

This script deletes ORN trackpoint data from staging area (l_orn_location,
l_orn_session, l_orn_track and l_orn_trackpoint).
*/

function delete_files_orn_trackpoint ($did,$fid) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

require_once $DBRoot."/action/delete_import_orn_trackpoint.php";

// SQL statement that selects all information on a file
$sql_file="
select
	imported_data
from l_file
where file_id = $1
";

$res=$db->query($sql_file, array($fid));
$imported_data=$res[0]['imported_data'];

if ($imported_data)
	return (delete_import_orn_trackpoint($fid));

return 0;
}
?>
