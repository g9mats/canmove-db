<?php
/*
Creator: Mats J Svensson, CAnMove

This script loads ORI count data from staging area (l_ori_count) into
destination tables (d_ori_count, d_ori_sector).
*/

function load_ori_count ($dataset_id,$file_id) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
require_once $DBRoot."/lib/oriAnimal.php";
require_once $DBRoot."/lib/oriCapture.php";
require_once $DBRoot."/lib/oriExperiment.php";
require_once $DBRoot."/lib/oriPhase.php";
require_once $DBRoot."/lib/oriCount.php";
require_once $DBRoot."/lib/oriSector.php";
require_once $DBRoot."/action/delete_import_ori_count.php";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
$animal = new oriAnimal();
$capture = new oriCapture();
$experiment = new oriExperiment();
$phase = new oriPhase();
$count = new oriCount();
$sector = new oriSector();

//require_once $DBRoot."/action/delete_import_ori_count.php";

// SQL statement that gets key values from file info
$sel_file="
select
	version
from l_file
where file_id = $1
";

// SQL statement that selects all rows from staging area
$sel_stage="
select * from l_ori_count
where dataset_id = $1
  and version = $2
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

// Get key values for file
$res = $db->query($sel_file, array($file_id));
$version = $res[0]['version'];

// Get column names for sector values
$sector_arr = $db->query($sel_var, array($dataset_id,"d_ori_sector"));

// Initialize all counters
$row_count=0; $err_count=0;
$ict_count=0; $uct_count=0;
$is_count=0; $us_count=0;

$err_arr = array();

// Initialize flag variables
$old_aid = 0;
$old_cid = 0;
$old_eid = 0;
$old_pid = 0;
$old_ctid = 0;
$old_sid = 0;

// Walk through all rows in l_ori_count
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
		$old_cid = 0; $old_eid = 0; $old_pid = 0; $old_ctid = 0; $old_sid = 0;
	}
	$cid=$capture->select($db, $aid, $row['capture_time']);
	if ($cid != $old_cid) {
		if ($cid == -1) {
			$err_arr[$err_count++]=$row['animal']." ".$row['capture_time'].
				": unknown capture";
			continue;
		}
		$old_cid = $cid;
		$old_eid = 0; $old_pid = 0; $old_ctid = 0; $old_sid = 0;
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
		$old_pid = 0; $old_ctid = 0; $old_sid = 0;
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
		$old_ctid = 0; $old_sid = 0;
	}
	$ctid=$count->select($db, $pid, $version);
	if ($ctid != $old_ctid) {
		if ($ctid == -1) {
			$ctid=$count->insert($db, $pid, $version,
				$row['funnel_line'],  $row['operator_id'],
				$row['activity'],  $row['d1'],
				$row['s'],  $row['r1'],
				$row['p1'],  $row['d2a'],
				$row['d2b'],  $row['r2'],
				$row['p2'],  $row['direction'],
				$row['count_remark']);
			$ict_count++;
			$sno=1;
			foreach ($sector_arr as $arr) {
				$sid=$sector->insert($db, $ctid, $sno,
					$row[$arr['load_name']]);
				$is_count++;
				$sno++;
			}
		} else {
			$uct_count+=$count->update($db, $ctid, $pid, $version,
				$row['funnel_line'],  $row['operator_id'],
				$row['activity'],  $row['d1'],
				$row['s'],  $row['r1'],
				$row['p1'],  $row['d2a'],
				$row['d2b'],  $row['r2'],
				$row['p2'],  $row['direction'],
				$row['count_remark']);
			$sno=1;
			foreach ($sector_arr as $arr) {
				$sid=$sector->select($db, $ctid, $sno);
				$us_count+=$sector->update($db, $sid, $ctid, $sno,
					$row[$arr['load_name']]);
				$sno++;
			}
		}
		$old_ctid = $ctid;
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
echo "Count data<br/>";
echo " - ".$ict_count." rows inserted<br/>";
echo " - ".$uct_count." rows updated<br/>";
echo "Sector data<br/>";
echo " - ".$is_count." rows inserted<br/>";
echo " - ".$us_count." rows updated<br/>";

if ($err_count > 0)
	return 1;

if (delete_import_ori_count($dataset_id,$version)) {
	echo "Temporary database storage<br/>";
	echo " - ".$row_count." rows deleted<br/>";
}

return 0;
}
?>
