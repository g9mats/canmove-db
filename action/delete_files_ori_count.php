<?php
/*
Creator: Mats J Svensson, CAnMove

This script deletes ORI count data from staging area (l_column and
l_ori_count).
*/

function delete_files_ori_count ($did,$fid) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

require_once $DBRoot."/action/delete_import_ori_count.php";

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
	return (delete_import_ori_count($did,$ver));

return 0;
}
?>
