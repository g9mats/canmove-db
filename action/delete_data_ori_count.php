<?php
/*
Creator: Mats J Svensson, CAnMove

This script deletes ORI count data from database (d_ori_count, d_ori_sector).
*/
error_reporting (E_ALL);

function delete_data_ori_count ($dataset_id) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL statement that deletes all sectors from database
$sql_sector="
delete from d_ori_sector
where count_id in (
	select count_id
	from d_ori_count
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
	)
";

// SQL statement that deletes all counts from database
$sql_count="
delete from d_ori_count
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

$res=$db->execute($sql_sector, array($dataset_id));
$res=$db->execute($sql_count, array($dataset_id));

return 0;
}
?>
