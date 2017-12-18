<?php
/*
Creator: Mats J Svensson, CAnMove

This script loads ORI capture data from staging area (l_ori_capture) into
destination tables (d_ori_animal, d_ori_animal_data, d_ori_capture,
d_ori_capture_data).
*/

function load_ori_capture ($dataset_id,$file_id) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
require_once $DBRoot."/lib/oriAnimal.php";
require_once $DBRoot."/lib/oriAnimalData.php";
require_once $DBRoot."/lib/oriCapture.php";
require_once $DBRoot."/lib/oriCaptureData.php";
require_once $DBRoot."/action/delete_import_ori_capture.php";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
$animal = new oriAnimal();
$animalData = new oriAnimalData();
$capture = new oriCapture();
$captureData = new oriCaptureData();

// SQL statement that selects all rows from staging area
$sql_selstage="
select * from l_ori_capture
where dataset_id = $1
order by animal, capture_time
";

// SQL statment that gets the ITIS tsn for a taxon
$sql_seltsn="
select tsn
from r_taxon
where complete_name = $1
order by name_usage desc
";

// SQL statment that selects column info for a specified table
$sql_selvar="
select
	p.data_id, p.load_name, r.column_name
from p_column p, r_data r
where p.data_id = r.data_id
  and p.dataset_id = $1
  and r.table_name = $2
order by p.order_no
";

// Get column names for animal optional data values
$animal_arr = $db->query($sql_selvar, array($dataset_id,"d_ori_animal_data"));

// Get column names for capture optional data values
$capture_arr = $db->query($sql_selvar, array($dataset_id,"d_ori_capture_data"));

// Initialize all counters
$row_count=0;
$ia_count=0; $ua_count=0; $iad_count=0; $uad_count=0;
$ic_count=0; $uc_count=0; $icd_count=0; $ucd_count=0;

// Initialize flag variables
$old_aid = 0;
$old_cid = 0;

// Walk through all rows in l_ori_capture
$stage = $db->query($sql_selstage, array($dataset_id));
foreach ($stage as $row) {
	$row_count++;
	$aid=$animal->select($db, $dataset_id, $row['animal']);
	if ($aid != $old_aid) {
		if ($res=$db->query($sql_seltsn, array($row['taxon'])))
			$tsn=$res[0]['tsn'];
		else {
			echo "Could not find Taxon: ".$row['taxon']."<br/>";
			return 1;
		}
		if ($aid == -1) {
			$aid=$animal->insert($db,$dataset_id,$row['animal'],
				$tsn,$row['taxon'],$row['animal_remark']);
			$ia_count++;
			$ordno=1;
			foreach ($animal_arr as $arr) {
				$adid=$animalData->insert($db, $aid, $ordno,
					$arr['data_id'], $row[$arr['load_name']]);
				$iad_count++;
				$ordno++;
			}
		} else {
			$ua_count+=$animal->update($db, $aid, $dataset_id, $row['animal'],
					$tsn,$row['taxon'],$row['animal_remark']);
			$ordno=1;
			foreach ($animal_arr as $arr) {
				$adid=$animalData->select($db, $aid, $ordno);
				$uad_count+=$animalData->update($db, $adid, $aid, $ordno,
					$arr['data_id'], $row[$arr['load_name']]);
				$ordno++;
			}
		}
		$old_aid = $aid;
		$old_cid = 0;
	}
	$cid=$capture->select($db, $aid, $row['capture_time']);
	if ($cid != $old_cid) {
		if ($cid == -1) {
			$cid=$capture->insert($db, $aid, $row['capture_time'],
					$row['latitude'], $row['longitude'], $row['location'],
					$row['operator_id'], $row['capture_remark']);
			$ic_count++;
			$ordno=1;
			foreach ($capture_arr as $arr) {
				$cdid=$captureData->insert($db, $cid, $ordno,
					$arr['data_id'], $row[$arr['load_name']]);
				$icd_count++;
				$ordno++;
			}
		} else {
			$uc_count+=$capture->update($db, $cid, $aid, $row['capture_time'],
					$row['latitude'], $row['longitude'], $row['location'],
					$row['operator_id'], $row['capture_remark']);
			$ordno=1;
			foreach ($capture_arr as $arr) {
				$cdid=$captureData->select($db, $cid, $ordno);
				$ucd_count+=$captureData->update($db, $cdid, $cid, $ordno,
					$arr['data_id'], $row[$arr['load_name']]);
				$ordno++;
			}
		}
		$old_cid = $cid;
	}
}

echo "Temporary database storage<br/>";
echo " - ".$row_count." rows read<br/>";
echo "Animal data<br/>";
echo " - ".$ia_count." rows inserted<br/>";
echo " - ".$ua_count." rows updated<br/>";
echo " - ".$iad_count." optional values inserted<br/>";
echo " - ".$uad_count." optional values updated<br/>";
echo "Capture data<br/>";
echo " - ".$ic_count." rows inserted<br/>";
echo " - ".$uc_count." rows updated<br/>";
echo " - ".$icd_count." optional values inserted<br/>";
echo " - ".$ucd_count." optional values updated<br/>";

if (delete_import_ori_capture($dataset_id)) {
	echo "Temporary database storage<br/>";
	echo " - ".$row_count." rows deleted<br/>";
}

return 0;
}
?>
