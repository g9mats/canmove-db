<?php
/*
Creator: Mats J Svensson, CAnMove

This script loads ORI estimation data from staging area (l_ori_estimation) into
destination tables (d_ori_estimation).
*/

function load_ori_estimation ($dataset_id,$file_id) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
require_once $DBRoot."/lib/oriAnimal.php";
require_once $DBRoot."/lib/oriCapture.php";
require_once $DBRoot."/lib/oriExperiment.php";
require_once $DBRoot."/lib/oriPhase.php";
require_once $DBRoot."/lib/oriEstimation.php";
require_once $DBRoot."/action/delete_import_ori_estimation.php";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
$animal = new oriAnimal();
$capture = new oriCapture();
$experiment = new oriExperiment();
$phase = new oriPhase();
$estimation = new oriEstimation();

//require_once $DBRoot."/action/delete_import_ori_estimation.php";

// SQL statement that gets key values from file info
$sel_file="
select
	version
from l_file
where file_id = $1
";

// SQL statement that selects all rows from staging area
$sel_stage="
select * from l_ori_estimation
where dataset_id = $1
  and version = $2
order by animal, capture_time, experiment_no, phase_no
";

// SQL statment that selects column info for a specified table
// Get key values for file
$res = $db->query($sel_file, array($file_id));
$version = $res[0]['version'];

// Initialize all counters
$row_count=0; $err_count=0;
$ies_count=0; $ues_count=0;

$err_arr = array();

// Initialize flag variables
$old_aid = 0;
$old_cid = 0;
$old_eid = 0;
$old_pid = 0;
$old_esid = 0;

// Walk through all rows in l_ori_estimation
$stage = $db->query($sel_stage, array($dataset_id,$version));
foreach ($stage as $row) {
	$row_count++;
	$aid=$animal->select($db, $dataset_id, $row['animal']);
	if ($aid != $old_aid) {
		if ($aid == -1) {
			$err_arr[$err_count++]=$row['animal'].": unknown animal";
			continue;
		}
		$old_aid = $aid;
		$old_cid = 0; $old_eid = 0; $old_pid = 0; $old_esid = 0;
	}
	$cid=$capture->select($db, $aid, $row['capture_time']);
	if ($cid != $old_cid) {
		if ($cid == -1) {
			$err_arr[$err_count++]=$row['animal']." ".$row['capture_time'].
				": unknown capture";
			continue;
		}
		$old_cid = $cid;
		$old_eid = 0; $old_pid = 0; $old_esid = 0;
	}
	$eid=$experiment->select($db, $aid, $cid, $row['experiment_no']);
	if ($eid != $old_eid) {
		if ($eid == -1) {
			$err_arr[$err_count++]=$row['animal']." ".$row['capture_time'].
				" ".$row['experiment_no'].
				": unknown experiment";
			continue;
		}
		$old_eid = $eid;
		$old_pid = 0; $old_esid = 0;
	}
	$pid=$phase->select($db, $eid, $row['phase_no']);
	if ($pid != $old_pid) {
		if ($pid == -1) {
			$err_arr[$err_count++]=$row['animal']." ".$row['capture_time'].
				" ".$row['experiment_no']." ".$row['phase_no'].
				": unknown phase";
			continue;
		}
		$old_pid = $pid;
		$old_esid = 0;
	}
	$esid=$estimation->select($db, $pid, $version);
	if ($esid != $old_esid) {
		if ($esid == -1) {
			$esid=$estimation->insert($db, $pid, $version,
				$row['operator_id'], $row['activity'],
				$row['concentration'], $row['direction'],
				$row['modality'], $row['act_plus_conc'],
				$row['estimation_remark']);
			$ies_count++;
		} else {
			$ues_count+=$estimation->update($db, $esid, $pid, $version,
				$row['operator_id'], $row['activity'],
				$row['concentration'], $row['direction'],
				$row['modality'], $row['act_plus_conc'],
				$row['estimation_remark']);
		}
		$old_esid = $esid;
	}
}

echo "Temporary database storage<br/>";
echo " - ".$row_count." rows read<br/>";
echo " - ".$err_count." errors found";
if ($err_count>20)
	echo ", first 20 shown<br/>";
else
	echo "<br/>";
for ($i=0; $i<min(20,$err_count); $i++)
	echo $err_arr[$i]."<br/>";
echo "Estimation data<br/>";
echo " - ".$ies_count." rows inserted<br/>";
echo " - ".$ues_count." rows updated<br/>";

if ($err_count > 0)
	return 1;

if (delete_import_ori_estimation($dataset_id,$version)) {
	echo "Temporary database storage<br/>";
	echo " - ".$row_count." rows deleted<br/>";
}

return 0;
}
?>
