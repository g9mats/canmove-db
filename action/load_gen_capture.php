<?php
/*
Creator: Mats J Svensson, CAnMove

This script loads GEN capture data from staging area (l_gen_capture) into
destination tables (d_gen_animal, d_gen_animal_data, d_gen_capture,
d_gen_capture_data, d_gen_track, d_gen_track_data, d_gen_device).
*/

function load_gen_capture ($dataset_id, $file_id) {

require "./canmove.inc";

require_once $DBRoot."/lib/DBLink.php";
require_once $DBRoot."/lib/genAnimal.php";
require_once $DBRoot."/lib/genAnimalData.php";
require_once $DBRoot."/lib/genCapture.php";
require_once $DBRoot."/lib/genCaptureData.php";
require_once $DBRoot."/lib/genTrack.php";
require_once $DBRoot."/lib/genTrackData.php";
require_once $DBRoot."/lib/genDevice.php";
require_once $DBRoot."/action/delete_import_gen_capture.php";

// Log on to database using common routine
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
$animal = new genAnimal();
$animalData = new genAnimalData();
$capture = new genCapture();
$captureData = new genCaptureData();
$track = new genTrack();
$trackData = new genTrackData();
$device = new genDevice();

// SQL statement that gets key values from file info
$sql_file="
select
	time_zone
from l_file
where file_id = $1
";

// SQL statement that selects all rows from staging area
$sql_selstage="
select * from l_gen_capture
where dataset_id = $1
order by animal, capture_time, track_event
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

// Get key values for file
$res = $db->query($sql_file, array($file_id));
$tz = $res[0]['time_zone'];
$sql_tz = "set time zone '".$tz."'";
$res = $db->execute($sql_tz);

// Get column names for animal optional data values
$animal_arr = $db->query($sql_selvar, array($dataset_id,"d_gen_animal_data"));

// Get column names for capture optional data values
$capture_arr = $db->query($sql_selvar, array($dataset_id,"d_gen_capture_data"));

// Get column names for track optional data values
$track_arr = $db->query($sql_selvar,array($dataset_id,"d_gen_track_data"));

// Get column names for device data values
$device_arr = $db->query($sql_selvar,array($dataset_id,"d_gen_device"));

// Initialize all counters
$row_count=0;
$ia_count=0; $ua_count=0; $iad_count=0; $uad_count=0;
$ic_count=0; $uc_count=0; $icd_count=0; $ucd_count=0;
$it_count=0; $ut_count=0; $utc_count=0; $itd_count=0; $utd_count=0;
$id_count=0; $ud_count=0;

// Initialize flag variables
$old_aid = 0;
$old_cid = 0;
$old_tid = 0;

// Walk through all rows in l_gen_capture
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
		$old_cid = 0; $old_tid = 0;
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
	if ($row['track_event']=="START") {
		$tid=$track->select($db, $aid, $cid);
		if ($tid == -1) {
			$tid=$track->insert($db, $aid, $cid, $row['track_remark']);
			$it_count++;
			$ordno=1;
			foreach ($track_arr as $arr) {
				$tdid=$trackData->insert($db, $tid, $ordno,
					$arr['data_id'], $row[$arr['load_name']]);
			$itd_count++;
			$ordno++;
			}
			$ordno=0;
			foreach ($device_arr as $arr) {
				if ($arr['column_name']=="device") {
					$ordno++;
					if ($row[$arr['load_name']] != "") {
						$devid=$device->insert($db, $tid,
									$row[$arr['load_name']], $ordno);
						$id_count++;
					}
				} else {
					$temp=$device->update_att($db, $tid, $ordno,
						$arr['column_name'], $row[$arr['load_name']]);
				}
			}
		} else {
			$ut_count+=$track->update($db, $tid, $aid, $cid,
								$row['track_remark']);
			$ordno=1;
			foreach ($track_arr as $arr) {
				$tdid=$trackData->select($db, $tid, $ordno);
				$utd_count+=$trackData->update($db, $tdid, $tid, $ordno,
					$arr['data_id'], $row[$arr['load_name']]);
				$ordno++;
			}
			$ordno=0;
			foreach ($device_arr as $arr) {
				if ($arr['column_name']=="device") {
					$ordno++;
					if ($row[$arr['load_name']] != "") {
						$dev_ins=false;
						$dev_upd=false;
						$dev=$device->select_att($db, $tid, $ordno, "device");
						if ($dev == "") {
							$devid=$device->insert($db, $tid,
										$row[$arr['load_name']], $ordno);
							$id_count++;
							$dev_ins=true;
						} else {
							$ud_count+=$device->update_att($db, $tid, $ordno,
											"device", $row[$arr['load_name']]);
							$dev_upd=true;
						}
					}
				} else {
					if ($row[$arr['load_name']] != "") {
						$temp=$device->update_att($db, $tid, $ordno,
							$arr['column_name'], $row[$arr['load_name']]);
						if (!$dev_ins && !$dev_upd)
							$ud_count++;
					}
				}
			}
		}
		$old_tid=$tid;
	} elseif (($row['track_event']=="END") && ($old_tid != 0)) {
		$utc_count+=$track->update_capture($db, $tid, $cid);
		$old_tid=0;
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
echo "Track data<br/>";
echo " - ".$it_count." rows inserted<br/>";
echo " - ".$ut_count." rows updated<br/>";
echo " - ".$utc_count." end captures updated<br/>";
echo " - ".$itd_count." optional values inserted<br/>";
echo " - ".$utd_count." optional values updated<br/>";
echo "Device data<br/>";
echo " - ".$id_count." rows inserted<br/>";
echo " - ".$ud_count." rows updated<br/>";

if (delete_import_gen_capture($dataset_id)) {
	echo "Temporary database storage<br/>";
	echo " - ".$row_count." rows deleted<br/>";
}

return 0;
}
?>
