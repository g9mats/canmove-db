<?php
/*
Creator: Mats J Svensson, CAnMove

This script deletes GEN capture data from database
(d_gen_animal, d_gen_animal_data, d_gen_capture, d_gen_capture_data,
d_gen_track, d_gen_track_data, d_gen_device).
*/
error_reporting (E_ALL);

function delete_data_gen_capture ($dataset_id) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL statement that checks for dependant trackpoint records
$sql_trackpoint="
select count(*) num
from l_file
where dataset_id = $1
  and data_subset = 'trackpoint'
  and data_status = 'final'
  and registered
";

// SQL statement that checks for dependant datapoint records
$sql_datapoint="
select count(*) num
from l_file
where dataset_id = $1
  and data_subset = 'datapoint'
  and data_status = 'final'
  and registered
";

// SQL statement that deletes all devices from database
$sql_device="
delete from d_gen_device
where track_id in (
	select track_id
	from d_gen_track
	where animal_id in (
		select animal_id
		from d_gen_animal
		where dataset_id = $1
		)
	)
";

// SQL statement that deletes all track data from database
$sql_track_data="
delete from d_gen_track_data
where track_id in (
	select track_id
	from d_gen_track
	where animal_id in (
		select animal_id
		from d_gen_animal
		where dataset_id = $1
		)
	)
";

// SQL statement that deletes all tracks from database
$sql_track="
delete from d_gen_track
where animal_id in (
	select animal_id
	from d_gen_animal
	where dataset_id = $1
	)
";

// SQL statement that deletes all capture data from database
$sql_capture_data="
delete from d_gen_capture_data
where capture_id in (
	select capture_id
	from d_gen_capture
	where animal_id in (
		select animal_id
		from d_gen_animal
		where dataset_id = $1
		)
	)
";

// SQL statement that deletes all captures from database
$sql_capture="
delete from d_gen_capture
where animal_id in (
	select animal_id
	from d_gen_animal
	where dataset_id = $1
	)
";

// SQL statement that deletes all animal data from database
$sql_animal_data="
delete from d_gen_animal_data
where animal_id in (
	select animal_id
	from d_gen_animal
	where dataset_id = $1
	)
";

// SQL statement that deletes all animals from database
$sql_animal="
delete from d_gen_animal
where dataset_id = $1
";

$res=$db->query($sql_trackpoint, array($dataset_id));
$tdata=$res[0]['num'];
$res=$db->query($sql_datapoint, array($dataset_id));
$ddata=$res[0]['num'];
if (($tdata>0)||($ddata>0)) {
	echo "<p>You can not delete capture data:<br/>";
	if ($tdata>0)
		echo "- There are still trackpoint data left.<br/>";
	if ($ddata>0)
		echo "- There are still datapoint data left.<br/>";
	echo "</p>";
	return 1;
}

$res=$db->execute($sql_device, array($dataset_id));
$res=$db->execute($sql_track_data, array($dataset_id));
$res=$db->execute($sql_track, array($dataset_id));
$res=$db->execute($sql_capture_data, array($dataset_id));
$res=$db->execute($sql_capture, array($dataset_id));
$res=$db->execute($sql_animal_data, array($dataset_id));
$res=$db->execute($sql_animal, array($dataset_id));

return 0;
}
?>
