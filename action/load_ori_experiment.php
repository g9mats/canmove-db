<?php
/*
Creator: Mats J Svensson, CAnMove

This script loads ORI experiment data from staging area (l_ori_experiment) into
destination tables (p_ori_context, p_ori_condition, p_ori_setup, p_ori_setup_phase, d_ori_experiment, d_ori_experiment_data, d_ori_phase, d_ori_phase_data).
*/

function load_ori_experiment ($dataset_id,$file_id) {

require "./canmove.inc";

require_once $DBRoot."/lib/DBLink.php";
require_once $DBRoot."/lib/oriAnimal.php";
require_once $DBRoot."/lib/oriCapture.php";
require_once $DBRoot."/lib/oriExperiment.php";
require_once $DBRoot."/lib/oriExperimentData.php";
require_once $DBRoot."/lib/oriPhase.php";
require_once $DBRoot."/lib/oriPhaseData.php";
require_once $DBRoot."/action/delete_import_ori_experiment.php";

// Log on to database using common routine
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
$animal = new oriAnimal();
$capture = new oriCapture();
$experiment = new oriExperiment();
$experimentData = new oriExperimentData();
$phase = new oriPhase();
$phaseData = new oriPhaseData();

// SQL statement that selects all rows from staging area
$sel_stage="
select * from l_ori_experiment
where dataset_id = $1
order by animal, capture_time, experiment_no, phase_no
";

// SQL statment that selects column info for a specified table
$sel_var="
select
	p.data_id, p.load_name, r.column_name
from p_column p, r_data r
where p.data_id = r.data_id
  and p.dataset_id = $1
  and r.table_name = $2
order by p.order_no
";

// SQL statement that gets setup information
$sel_setup="
select
	setup_id
from p_ori_setup
where dataset_id = $1
  and setup = $2
";

// SQL statement that insert NEW setups
$ins_setup="
insert into p_ori_setup (
	dataset_id, setup
)
select cast ($1 as integer), setup
from l_ori_experiment
where dataset_id = $1
except
select dataset_id, setup
from p_ori_setup
where dataset_id = $1
";

// SQL statement that deletes SOME setups from database
$del_setup="
delete from p_ori_setup
where dataset_id = $1
  and setup_id in (
	select setup_id from p_ori_setup
	where dataset_id = $1
	except
	select setup_id from d_ori_experiment
	where animal_id in (
		select animal_id from d_ori_animal
		where dataset_id = $1
		)
	)
";

// SQL statement that inserts setup phases
$ins_phase="
insert into p_ori_setup_phase (
	setup_id, phase_no, context_id
)
select
	s.setup_id,
	p.phase_no,
	c.context_id
from p_ori_setup s, l_ori_experiment p, p_ori_context c COND_FROM
where s.dataset_id = p.dataset_id
  and s.dataset_id = c.dataset_id
  and s.setup = p.setup
  and s.dataset_id = $1
  COND_WHERE
group by s.setup_id,p.phase_no,c.context_id COND_LIST
order by s.setup_id,p.phase_no,c.context_id COND_LIST
";

// SQL statement that deletes all setup phases from database
$del_phase="
delete from p_ori_setup_phase
where setup_id in (
	select setup_id
	from p_ori_setup
	where dataset_id = $1
	)
";

// SQL statement that gets information on all contexts
$sel_cont="
select distinct COND_LIST
from l_ori_experiment
where dataset_id = $1
order by COND_LIST
";

// SQL statement that inserts a context record
$ins_cont="
insert into p_ori_context (
	dataset_id, context
) values ($1, $2)
";

// SQL statement that deletes all contexts from database
$del_cont="
delete from p_ori_context
where dataset_id = $1
";

// SQL statement that gets information on all condition columns
$sel_cond="
select p.header, p.load_name
from p_column p, r_data r
where p.data_id = r.data_id
  and p.dataset_id = $1
  and r.table_name = 'p_ori_condition'
order by p.order_no
";

// SQL statement that inserts a condition record
$ins_cond="
insert into p_ori_condition (
	context_id, condition_type, condition_value
) select distinct context_id, $3, $4
from p_ori_context
where dataset_id = $1
  and context = $2
";

// SQL statement that deletes all conditions from database
$del_cond="
delete from p_ori_condition
where context_id in (
	select context_id
	from p_ori_context
	where dataset_id = $1
	)
";

// Insert setups
$res=$db->execute($ins_setup, array($dataset_id));

// Get column names for experiment optional data values
$experiment_arr = $db->query($sel_var, array($dataset_id,"d_ori_experiment_data"));

// Get column names for phase optional data values
$phase_arr = $db->query($sel_var, array($dataset_id,"d_ori_phase_data"));

$row_count=0; $err_count=0;
$ie_count=0; $ue_count=0; $ied_count=0; $ued_count=0;
$ip_count=0; $up_count=0; $ipd_count=0; $upd_count=0;

$err_arr = array();

// Initialize flag variables
$old_aid = 0;
$old_cid = 0;
$old_eid = 0;
$old_pid = 0;

// Initialize all counters
// Walk through all rows in l_ori_experiment
$stage = $db->query($sel_stage, array($dataset_id));
foreach ($stage as $row) {
	$row_count++;
	$aid=$animal->select($db, $dataset_id, $row['animal']);
	if ($aid != $old_aid) {
		if ($aid == -1) {
			$err_arr[$err_count++]=$row['animal'].": unknown animal";
			continue;
		}
		$old_aid = $aid;
		$old_cid = 0; $old_eid = 0; $old_pid = 0;
	}
	$cid=$capture->select($db, $aid, $row['capture_time']);
	if ($cid != $old_cid) {
		if ($cid == -1) {
			$err_arr[$err_count++]=$row['animal']." ".$row['capture_time'].
				": unknown capture time";
			continue;
		}
		$old_cid = $cid;
		$old_eid = 0; $old_pid = 0;
	}
	$eid=$experiment->select($db, $aid, $cid, $row['experiment_no']);
	if ($eid != $old_eid) {
		$res=$db->query($sel_setup, array($dataset_id,$row['setup']));
		$sid = $res[0]['setup_id'];
		if ($eid == -1) {
			$eid=$experiment->insert($db, $aid, $cid, $sid,
				$row['experiment_no'], $row['experiment_type'],
				$row['cage_top_diameter'], $row['cage_height'],
				$row['sensor_type'],
				$row['data_processing'], $row['data_format'],
				$row['latitude'], $row['longitude'],
				$row['location'], $row['operator_id'],
				$row['measurement_time'], $row['experiment_remark']);
			$ie_count++;
			$ordno=1;
			foreach ($experiment_arr as $arr) {
				$edid=$experimentData->insert($db, $eid, $ordno,
					$arr['data_id'], $row[$arr['load_name']]);
				$ied_count++;
				$ordno++;
			}
		} else {
			$ue_count+=$experiment->update($db, $eid, $aid, $cid, $sid,
				$row['experiment_no'], $row['experiment_type'],
				$row['cage_top_diameter'], $row['cage_height'],
				$row['sensor_type'],
				$row['data_processing'], $row['data_format'],
				$row['latitude'], $row['longitude'],
				$row['location'], $row['operator_id'],
				$row['measurement_time'],  $row['experiment_remark']);
			$ordno=1;
			foreach ($experiment_arr as $arr) {
				$edid=$experimentData->select($db, $eid, $ordno);
				$ued_count+=$experimentData->update($db, $edid, $eid, $ordno,
					$arr['data_id'], $row[$arr['load_name']]);
				$ordno++;
			}
		}
		$old_eid = $eid;
		$old_pid = 0;
	}
	$pid=$phase->select($db, $eid, $row['phase_no']);
	if ($pid != $old_pid) {
		if ($pid == -1) {
			$pid=$phase->insert($db, $eid, $row['phase_no'],
				$row['start_time'], $row['end_time'],
				$row['middle_time'],  $row['phase_remark']);
			$ip_count++;
			$ordno=1;
			foreach ($phase_arr as $arr) {
				$pdid=$phaseData->insert($db, $pid, $ordno,
					$arr['data_id'], $row[$arr['load_name']]);
				$ipd_count++;
				$ordno++;
			}
		} else {
			$up_count+=$phase->update($db, $pid, $eid, $row['phase_no'],
				$row['start_time'], $row['end_time'],
				$row['middle_time'],  $row['phase_remark']);
			$ordno=1;
			foreach ($phase_arr as $arr) {
				$pdid=$phaseData->select($db, $pid, $ordno);
				$upd_count+=$phaseData->update($db, $pdid, $pid, $ordno,
					$arr['data_id'], $row[$arr['load_name']]);
				$ordno++;
			}
		}
		$old_pid = $pid;
	}
}

// Delete unused setup names
$res=$db->execute($del_setup, array($dataset_id));

echo "Temporary database storage<br/>";
echo " - ".$row_count." rows read<br/>";
echo " - ".$err_count." errors found";
if ($err_count>20)
	echo ", first 20 shown<br/>";
else
	echo "<br/>";
for ($i=0; $i<min(20,$err_count); $i++)
	echo $err_arr[$i]."<br/>";
echo "Experiment data<br/>";
echo " - ".$ie_count." rows inserted<br/>";
echo " - ".$ue_count." rows updated<br/>";
echo " - ".$ied_count." optional values inserted<br/>";
echo " - ".$ued_count." optional values updated<br/>";
echo "Phase data<br/>";
echo " - ".$ip_count." rows inserted<br/>";
echo " - ".$up_count." rows updated<br/>";
echo " - ".$ipd_count." optional values inserted<br/>";
echo " - ".$upd_count." optional values updated<br/>";

if ($err_count > 0)
	return 1;

// Delete the setup information except for the actual setup names
$res=$db->execute($del_phase, array($dataset_id));
$res=$db->execute($del_cond, array($dataset_id));
$res=$db->execute($del_cont, array($dataset_id));

// Get condition columns information
$cond_arr=$db->query($sel_cond,array($dataset_id));
$cond_list="";
for ($i=0;$i<count($cond_arr);$i++) {
	$cond_list .= ",".$cond_arr[$i]['load_name'];
}

// Get Context information
$sel_cont = str_replace ("COND_LIST", ltrim($cond_list,","), $sel_cont);
$cont_arr=$db->query($sel_cont,array($dataset_id));

// Insert contexts and conditions
for ($i=0;$i<count($cont_arr);$i++) {
	$res=$db->execute($ins_cont, array($dataset_id,$i+1));
	for ($j=0;$j<count($cond_arr);$j++) {
		$res=$db->execute($ins_cond,
			array(
				$dataset_id,
				$i+1,
				$cond_arr[$j]['header'],
				$cont_arr[$i][$cond_arr[$j]['load_name']]
			));
	}
}

// Insert setup phases
$cond_from=""; $cond_where="";
for ($i=0;$i<count($cond_arr);$i++) {
	$cond_from .= ",p_ori_condition d".$i;
	$cond_where .= " and c.context_id=d".$i.".context_id";
	$cond_where .= " and p.".$cond_arr[$i]['load_name']."=d".$i.".condition_value";
	$cond_where .= " and d".$i.".condition_type='".$cond_arr[$i]['header']."'";
}
$ins_phase = str_replace ("COND_LIST", $cond_list, $ins_phase);
$ins_phase = str_replace ("COND_FROM", $cond_from, $ins_phase);
$ins_phase = str_replace ("COND_WHERE", $cond_where, $ins_phase);
$res=$db->execute($ins_phase, array($dataset_id));

if (delete_import_ori_experiment($dataset_id)) {
	echo "Temporary database storage<br/>";
	echo " - ".$row_count." rows deleted<br/>";
}

return 0;
}
?>
