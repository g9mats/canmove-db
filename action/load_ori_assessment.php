<?php
/*
Creator: Mats J Svensson, CAnMove

This script loads ORI assessment data from staging area (l_ori_assessment) into
destination tables (d_ori_assessment, d_ori_assessment_data).
*/

function load_ori_assessment ($dataset_id,$file_id) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
require_once $DBRoot."/lib/oriAnimal.php";
require_once $DBRoot."/lib/oriCapture.php";
require_once $DBRoot."/lib/oriAssessment.php";
require_once $DBRoot."/lib/oriAssessmentData.php";
require_once $DBRoot."/action/delete_import_ori_assessment.php";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
$animal = new oriAnimal();
$capture = new oriCapture();
$assessment = new oriAssessment();
$assessmentData = new oriAssessmentData();

// SQL statement that selects all rows from staging area
$sel_stage="
select * from l_ori_assessment
where dataset_id = $1
order by animal, capture_time, assessment_no
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

// Get column names for assessment optional data values
$assessment_arr = $db->query($sel_var, array($dataset_id,"d_ori_assessment_data"));

$row_count=0; $err_count=0;
$ia_count=0; $ua_count=0; $iad_count=0; $uad_count=0;

$err_arr = array();

// Initialize flag variables
$old_aid = 0;
$old_cid = 0;
$old_assid = 0;

// Initialize all counters
// Walk through all rows in l_ori_assessment
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
		$old_cid = 0; $old_assid = 0;
	}
	$cid=$capture->select($db, $aid, $row['capture_time']);
	if ($cid != $old_cid) {
		if ($cid == -1) {
			$err_arr[$err_count++]=$row['animal']." ".$row['capture_time'].
				": unknown capture";
			continue;
		}
		$old_cid = $cid;
		$old_assid = 0;
	}
	$assid=$assessment->select($db, $aid, $cid, $row['assessment_no']);
	if ($assid != $old_assid) {
		if ($assid == -1) {
			$assid=$assessment->insert($db, $aid, $cid,
				$row['assessment_no'], $row['assessment_time'],
				$row['operator_id'], $row['assessment_remark']);
			$ia_count++;
			$ordno=1;
			foreach ($assessment_arr as $arr) {
				$edid=$assessmentData->insert($db, $assid, $ordno,
					$arr['data_id'], $row[$arr['load_name']]);
				$iad_count++;
				$ordno++;
			}
		} else {
			$ua_count+=$assessment->update($db, $assid, $aid, $cid,
				$row['assessment_no'], $row['assessment_time'],
				$row['operator_id'], $row['assessment_remark']);
			$ordno=1;
			foreach ($assessment_arr as $arr) {
				$edid=$assessmentData->select($db, $assid, $ordno);
				$uad_count+=$assessmentData->update($db, $edid, $assid, $ordno,
					$arr['data_id'], $row[$arr['load_name']]);
				$ordno++;
			}
		}
		$old_assid = $assid;
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
echo "Assessment data<br/>";
echo " - ".$ia_count." rows inserted<br/>";
echo " - ".$ua_count." rows updated<br/>";
echo " - ".$iad_count." optional values inserted<br/>";
echo " - ".$uad_count." optional values updated<br/>";

if ($err_count > 0)
	return 1;

if (delete_import_ori_assessment($dataset_id)) {
	echo "Temporary database storage<br/>";
	echo " - ".$row_count." rows deleted<br/>";
}

return 0;
}
?>
