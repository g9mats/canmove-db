<?php
/*
Creator: Mats J Svensson, CAnMove

This script deletes ORI experiment data from database
(p_ori_context, p_ori_condition, p_ori_setup, p_ori_setup_phase,
d_ori_experiment, d_ori_experiment_data, d_ori_phase, d_ori_phase_data).
*/
error_reporting (E_ALL);

function delete_data_ori_experiment ($dataset_id) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL statement that checks for dependant count records
$sql_count="
select count(*) num
from l_file
where dataset_id = $1
  and data_subset = 'count'
  and data_status = 'final'
  and imported
";

// SQL statement that checks for dependant estimation records
$sql_estimation="
select count(*) num
from l_file
where dataset_id = $1
  and data_subset = 'estimation'
  and data_status = 'final'
  and imported
";

// SQL statement that checks for dependant activity_log records
$sql_activity_log="
select count(*) num
from l_file
where dataset_id = $1
  and data_subset = 'activity_log'
  and data_status = 'final'
  and imported
";

// SQL statement that deletes all phase data from database
$sql_phase_data="
delete from d_ori_phase_data
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

// SQL statement that deletes all phases from database
$sql_phase="
delete from d_ori_phase
where experiment_id in (
	select experiment_id
	from d_ori_experiment
	where animal_id in (
		select animal_id
		from d_ori_animal
		where dataset_id = $1
		)
	)
";

// SQL statement that deletes all experiment data from database
$sql_experiment_data="
delete from d_ori_experiment_data
where experiment_id in (
	select experiment_id
	from d_ori_experiment
	where animal_id in (
		select animal_id
		from d_ori_animal
		where dataset_id = $1
		)
	)
";

// SQL statement that deletes all experiments from database
$sql_experiment="
delete from d_ori_experiment
where animal_id in (
	select animal_id
	from d_ori_animal
	where dataset_id = $1
	)
";

// SQL statement that deletes all setup phases from database
$sql_phase="
delete from p_ori_setup_phase
where setup_id in (
	select setup_id
	from p_ori_setup
	where dataset_id = $1
)
";

// SQL statement that deletes all setups from database
$sql_setup="
delete from p_ori_setup
where dataset_id = $1
";

// SQL statement that deletes all conditions from database
$sql_cond="
delete from p_ori_condition
where context_id in (
	select context_id
	from p_ori_context
	where dataset_id = $1
)
";

// SQL statement that deletes all contexts from database
$sql_cont="
delete from p_ori_context
where dataset_id = $1
";

$res=$db->query($sql_count, array($dataset_id));
$cdata=$res[0]['num'];
$res=$db->query($sql_estimation, array($dataset_id));
$edata=$res[0]['num'];
$res=$db->query($sql_activity_log, array($dataset_id));
$adata=$res[0]['num'];
if (($cdata>0)||($edata>0)||($adata>0)) {
	echo "<p>You can not delete experiment data:<br/>";
	if ($cdata>0)
		echo "- There are still count data left.<br/>";
	if ($edata>0)
		echo "- There are still estimation data left.<br/>";
	if ($adata>0)
		echo "- There are still activity log data left.<br/>";
	echo "</p>";
	return 1;
}

$res=$db->execute($sql_phase_data, array($dataset_id));
$res=$db->execute($sql_phase, array($dataset_id));
$res=$db->execute($sql_experiment_data, array($dataset_id));
$res=$db->execute($sql_experiment, array($dataset_id));
$res=$db->execute($sql_phase, array($dataset_id));
$res=$db->execute($sql_setup, array($dataset_id));
$res=$db->execute($sql_cond, array($dataset_id));
$res=$db->execute($sql_cont, array($dataset_id));

return 0;
}
?>
