<?php
/*
Creator: Mats J Svensson, CAnMove

This script deletes ORI activity log data from staging area
(l_ori_activity_log and l_column).
*/

function delete_files_ori_activity_log ($did,$fid) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

require_once $DBRoot."/action/delete_import_ori_activity_log.php";

// SQL statement that selects all information on a file
$sql_file="
select
	version,
	imported_data
from l_file
where file_id = $1
";

$res=$db->query($sql_file, array($fid));
$ver=$res[0]['version'];
$imported_data=$res[0]['imported_data'];

if ($imported_data)
	return (delete_import_ori_activity_log($did,$ver));

return 0;
}
?>
