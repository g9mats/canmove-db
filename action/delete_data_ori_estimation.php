<?php
/*
Creator: Mats J Svensson, CAnMove

This script deletes ORI estimation data from database (d_ori_estimation).
*/
error_reporting (E_ALL);

function delete_data_ori_estimation ($dataset_id) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL statement that deletes all estimations from database
$sql_estimation="
delete from d_ori_estimation
where phase_id in (
	select phase_id
	from d_ori_phase
	where experiment_id in (
		select experiment_id
		from d_ori_experiment
		where animal_id in (
			select animal_id
			from d_ori_animal
			where dataset_id = $1
			)
		)
	)
";

$res=$db->execute($sql_estimation, array($dataset_id));

return 0;
}
?>
