<?php
/*
Creator: Mats J Svensson, CAnMove

This script deletes ORN trackpoint data from staging area (l_orn_location,
l_orn_session, l_orn_track and l_orn_trackpoint).
*/

function delete_import_orn_trackpoint ($fid) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL statement that deletes all data rows from trackpoint staging area
$sql_trackpoint="
delete from l_orn_trackpoint
where file_id = $1
";

// SQL statement that deletes all data rows from track staging area
$sql_track="
delete from l_orn_track
where file_id = $1
";

// SQL statement that deletes all data rows from session staging area
$sql_session="
delete from l_orn_session
where file_id = $1
";

// SQL statement that deletes all data rows from location staging area
$sql_location="
delete from l_orn_location
where file_id = $1
";

if ($res=$db->execute($sql_trackpoint, array($fid))) {
	if ($res=$db->execute($sql_track, array($fid))) {
		if ($res=$db->execute($sql_session, array($fid))) {
			if ($res=$db->execute($sql_location, array($fid))) {
				return 0;
			}
		}
	}
}
return 1;

}
?>
