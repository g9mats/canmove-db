<?php
/*
Creator: Mats J Svensson, CAnMove

This script deletes ORN trackpoint data from database
(d_orn_session, d_orn_track, d_orn_trackpoint).
*/
error_reporting (E_ALL);

function delete_data_orn_trackpoint ($dataset_id) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL statement that deletes all wind data records from database
$sql_winddata="
delete from d_orn_wind_data
where wind_profile_id in (
	select wind_profile_id
	from d_orn_wind_profile
	where session_id in (
		select session_id
		from d_orn_session
		where dataset_id = $1
		)
	)
";

// SQL statement that deletes all wind profile data from database
$sql_windprofile="
delete from d_orn_wind_profile
where session_id in (
	select session_id
	from d_orn_session
	where dataset_id = $1
	)
";

// SQL statement that deletes all trackpoint data from database
$sql_trackpoint="
delete from d_orn_trackpoint
where track_id in (
	select track_id
	from d_orn_track
	where session_id in (
		select session_id
		from d_orn_session
		where dataset_id = $1
		)
	)
";

// SQL statement that deletes all track data from database
$sql_track="
delete from d_orn_track
where session_id in (
	select session_id
	from d_orn_session
	where dataset_id = $1
	)
";

// SQL statement that deletes all sessions from database
$sql_session="
delete from d_orn_session
where dataset_id = $1
";

// SQL statements that deletes all data from staging area that is not
// deleted by the calling script (delete_data2.php).
$sql_stage1="
delete from l_orn_location
where dataset_id = $1
";
$sql_stage2="
delete from l_orn_session
where dataset_id = $1
";
$sql_stage3="
delete from l_orn_track
where dataset_id = $1
";

$res=$db->execute($sql_winddata, array($dataset_id));
$res=$db->execute($sql_windprofile, array($dataset_id));
$res=$db->execute($sql_trackpoint, array($dataset_id));
$res=$db->execute($sql_track, array($dataset_id));
$res=$db->execute($sql_session, array($dataset_id));
$res=$db->execute($sql_stage1, array($dataset_id));
$res=$db->execute($sql_stage2, array($dataset_id));
$res=$db->execute($sql_stage3, array($dataset_id));

return 0;
}
?>
