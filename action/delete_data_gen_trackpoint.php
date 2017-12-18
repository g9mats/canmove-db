<?php
/*
Creator: Mats J Svensson, CAnMove

This script deletes GEN trackpoint data from database (d_gen_trackpoint).
*/
error_reporting (E_ALL);

function delete_data_gen_trackpoint ($dataset_id) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL statement that delete all trackpoint records
$sql_trackpoint="
delete from d_gen_trackpoint
where device_id in (
	select device_id
	from d_gen_device
	where track_id in (
		select track_id
		from d_gen_track
		where animal_id in (
			select animal_id
			from d_gen_animal
			where dataset_id = $1
			)
		)
	)
";

$res=$db->execute($sql_trackpoint, array($dataset_id));

return 0;
}
?>
