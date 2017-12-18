<?php
/*
Creator: Mats J Svensson, CAnMove

This script deletes ORI capture data from staging area (l_column and
l_ori_capture).
*/

function delete_files_ori_capture ($did,$fid) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

require_once $DBRoot."/action/delete_import_ori_capture.php";

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
	return (delete_import_ori_capture($did));

return 0;
}
?>
