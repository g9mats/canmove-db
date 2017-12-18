<?php
/*
Creator: Mats J Svensson, CAnMove

This script deletes ORI activity log data from database (d_ori_activity_log).
*/
error_reporting (E_ALL);

function delete_data_ori_activity_log ($dataset_id) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL statement that deletes all activity log data from database
$sql_activity_log="
delete from d_ori_activity_log
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

$res=$db->execute($sql_activity_log, array($dataset_id));

return 0;
}
?>
