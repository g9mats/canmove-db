<?php
/*
Creator: Mats J Svensson, CAnMove

This script deletes NBL trackpoint data from database
(p_nbl_context, p_nbl_condition, p_nbl_setup, p_nbl_setup_phase,
d_nbl_recording, d_nbl_track, d_nbl_track_data,
d_nbl_recording, d_nbl_trackpoint).
*/
error_reporting (E_ALL);

function delete_data_nbl_trackpoint ($dataset_id) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL statement that deletes all trackpoints from database
$sql_trackpoint="
delete from d_nbl_trackpoint
where track_id in (
	select track_id
	from d_nbl_track
	where recording_id in (
		select recording_id
		from d_nbl_recording
		where dataset_id = $1
	)
)
";

// SQL statement that deletes all files from database
$sql_file="
delete from d_nbl_file
where recording_id in (
	select recording_id
	from d_nbl_recording
	where dataset_id = $1
)
";

// SQL statement that deletes all track data from database
$sql_track_data="
delete from d_nbl_track_data
where track_id in (
	select track_id
	from d_nbl_track
	where recording_id in (
		select recording_id
		from d_nbl_recording
		where dataset_id = $1
	)
)
";

// SQL statement that deletes all tracks from database
$sql_track="
delete from d_nbl_track
where recording_id in (
	select recording_id
	from d_nbl_recording
	where dataset_id = $1
)
";

// SQL statement that deletes all recordings from database
$sql_rec="
delete from d_nbl_recording
where dataset_id = $1
";

// SQL statement that deletes all setup phases from database
$sql_phase="
delete from p_nbl_setup_phase
where setup_id in (
	select setup_id
	from p_nbl_setup
	where dataset_id = $1
)
";

// SQL statement that deletes all setups from database
$sql_setup="
delete from p_nbl_setup
where dataset_id = $1
";

// SQL statement that deletes all conditions from database
$sql_cond="
delete from p_nbl_condition
where context_id in (
	select context_id
	from p_nbl_context
	where dataset_id = $1
)
";

// SQL statement that deletes all contexts from database
$sql_cont="
delete from p_nbl_context
where dataset_id = $1
";

$res=$db->execute($sql_trackpoint, array($dataset_id));
$res=$db->execute($sql_file, array($dataset_id));
$res=$db->execute($sql_track_data, array($dataset_id));
$res=$db->execute($sql_track, array($dataset_id));
$res=$db->execute($sql_rec, array($dataset_id));
$res=$db->execute($sql_phase, array($dataset_id));
$res=$db->execute($sql_setup, array($dataset_id));
$res=$db->execute($sql_cond, array($dataset_id));
$res=$db->execute($sql_cont, array($dataset_id));

return 0;
}
?>
