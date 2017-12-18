<?php
/*
Creator: Mats J Svensson, CAnMove

This script loads ORN trackpoint data from staging area (l_orn_location,
l_orn_session, l_orn_track, l_orn_trackpoint) into destination tables
(r_orn_location, r_orn_taxon, d_orn_session, d_orn_track, d_orn_trackpoint).
*/

function load_orn_trackpoint ($dataset_id, $file_id) {

require "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";
require_once $DBRoot."/lib/ornLocation.php";
require_once $DBRoot."/lib/ornSession.php";
require_once $DBRoot."/lib/ornTrack.php";
require_once $DBRoot."/lib/ornTrackpoint.php";
require_once $DBRoot."/lib/ornWindProfile.php";
require_once $DBRoot."/lib/ornWindData.php";
require_once $DBRoot."/action/delete_import_orn_trackpoint.php";

// Log on to database using common routine
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
$ornloc = new ornLocation();
$session = new ornSession();
$track = new ornTrack();
$trackpoint = new ornTrackpoint();
$windprofile = new ornWindProfile();
$winddata = new ornWindData();

// SQL statement that gets key values from file info
$sql_file="
select
	original_name
from l_file
where file_id = $1
";

// SQL statements that selects all rows from staging area
$sql_selloc="
select * from l_orn_location
where file_id = $1
";
$sql_selsess="
select * from l_orn_session
where file_id = $1
";
$sql_seltrack="
select * from l_orn_track
where file_id = $1
order by track_no
";
$sql_selpoint="
select * from l_orn_trackpoint
where file_id = $1
  and track_no = $2
order by trackpoint_no
";

// SQL statements that deletes old rows in database that do not exist in file
// SQL statement that deletes extra wind data records for a specific track
$sql_delwdata="
delete from d_orn_wind_data
where wind_profile_id = $1
  and wind_data_no > $2
";
// SQL statement that deletes extra trackpoints for a specific track
$sql_delpoint="
delete from d_orn_trackpoint
where track_id = $1
  and trackpoint_no > $2
";
// SQL statement that deletes all wind data records for a all extra profiles
$sql_delwinddata="
delete from d_orn_wind_data
where wind_profile_id in (
	select wind_profile_id from d_orn_wind_profile
	where session_id = $1
	  and wind_profile_no > $2
	)
";
// SQL statement that deletes all extra wind profiles
$sql_delwindprofile="
delete from d_orn_wind_profile
where session_id = $1
  and wind_profile_no > $2
";
// SQL statement that deletes all trackpoints for a all extra tracks
$sql_deltrackpoint="
delete from d_orn_trackpoint
where track_id in (
	select track_id from d_orn_track
	where session_id = $1
	  and track_no in (
		select track_no from d_orn_track
		where session_id = $1
		except
		select track_no from l_orn_track
		where file_id = $2
		)
	)
";
// SQL statement that deletes all extra tracks
$sql_deltrack="
delete from d_orn_track
where session_id = $1
  and track_no in (
	select track_no from d_orn_track
	where session_id = $1
	except
	select track_no from l_orn_track
	where file_id = $2
	)
";

// Get key values for file
$res = $db->query($sql_file, array($file_id));
$file_name = $res[0]['original_name'];
echo "File: ".$file_name."</br>\n";

// Check for previous use of current location and insert if not
$lres = $db->query($sql_selloc, array($file_id));
$lrow = $lres[0];
$lid = $ornloc->select($db, $lrow['location']);
if ($lid == -1) {
	$lid=$ornloc->insert($db, $lrow['location'], $lrow['latitude'],
		$lrow['longitude'], $lrow['altitude'], $lrow['declination']);
}
//echo "Location: ".$lrow['location']."</br>\n";

// Insert or update current session
$sres = $db->query($sql_selsess, array($file_id));
$srow = $sres[0];
$sid = $session->select($db, $dataset_id,
					$lrow['location'], $srow['session_time']);
//echo "Session id: ".$sid."</br>\n";
if ($sid == -1) {
//echo "Insert session: ";
	$sid=$session->insert($db, $dataset_id,
		$lrow['location'], $srow['session_time'], $file_id,
		$lrow['latitude'], $lrow['longitude'], $lrow['altitude'],
		$lrow['declination'], $srow['height_datum'], $srow['height_source'],
		$srow['wind_source'], $srow['anemometer_height'],
		$srow['taxa_file'], $srow['activity_file']);
//echo $sid."</br>\n";
} else {
//echo "Update session: ".$sid."</br>\n";
	$sunum=$session->update($db, $sid, $dataset_id,
		$lrow['location'], $srow['session_time'], $file_id,
		$lrow['latitude'], $lrow['longitude'], $lrow['altitude'],
		$lrow['declination'], $srow['height_datum'], $srow['height_source'],
		$srow['wind_source'], $srow['anemometer_height'],
		$srow['taxa_file'], $srow['activity_file']);
}

$wp_no=0;	// Wind Profile Number
$tracknum=0; // Tracks read from staging area
$tinum=0; $pinum=0; $wpinum=0; $wdinum=0; 	// Keep track of data rows inserted
$tunum=0; $punum=0; $wpunum=0; $wdunum=0; 	// Keep track of data rows updated
$tdnum=0; $pdnum=0; $wpdnum=0; $wddnum=0; 	// Keep track of data rows deleted
$tres = $db->query($sql_seltrack, array($file_id));
foreach ($tres as $trow) {
//echo "Track No: ".$trow['track_no']."</br>\n";
	$tracknum++;
	$tid=$track->select($db,$sid,$trow['track_no']);
	if ($tid == -1) {
//echo "Insert track: ";
		$tid=$track->insert($db,$sid,$trow['track_no'],$trow['start_time'],
			$trow['itis_tsn'],$trow['taxon'],$trow['species_no'],
			$trow['english_name'],$trow['swedish_name'],
			$trow['wind_direction'],$trow['wind_speed'],
			$trow['barometer'],$trow['temperature'],
			$trow['quantity'],$trow['sex'],$trow['age'],$trow['crop'],
			$trow['flight_style'],$trow['activity'],$trow['remark']);
//echo $tid."</br>\n";
		$tinum++;
	} else {
//echo "Update track: ".$tid."</br>\n";
		$tunum+=$track->update($db,$tid,$sid,$trow['track_no'],
			$trow['start_time'],
			$trow['itis_tsn'],$trow['taxon'],$trow['species_no'],
			$trow['english_name'],$trow['swedish_name'],
			$trow['wind_direction'],$trow['wind_speed'],
			$trow['barometer'],$trow['temperature'],
			$trow['quantity'],$trow['sex'],$trow['age'],$trow['crop'],
			$trow['flight_style'],$trow['activity'],$trow['remark']);
	}
	if ($trow['species_no'] == -1) {
		$wp_no++;
//echo "Wind profile ".$wp_no."</br>\n";
		$wpid=$windprofile->select($db,$sid,$wp_no);
		if ($wpid == -1) {
			$wpid=$windprofile->insert($db,$sid,$wp_no,$trow['start_time']);
//echo $wpid."</br>\n";
			$wpinum++;
		} else {
//echo "Update wind profile: ".$wpid."</br>\n";
			$wpunum+=$windprofile->update($db,$wpid,$sid,$wp_no,
				$trow['start_time']);
		}
	}
	$pres = $db->query($sql_selpoint, array($file_id,$trow['track_no']));
	foreach ($pres as $prow) {
//echo "Trackpoint No: ".$prow['trackpoint_no']."</br>\n";
		$pid=$trackpoint->select($db,$tid,$prow['trackpoint_no']);
		if ($pid == -1) {
//echo "Insert trackpoint: ";
			$pid=$trackpoint->insert($db,$tid,$prow['trackpoint_no'],
				$prow['elapsed_time'],$prow['x'],$prow['y'],$prow['z'],
				$prow['air_density']);
//echo $pid."</br>\n";
			$pinum++;
		} else {
//echo "Update trackpoint: ".$pid."</br>\n";
			$punum+=$trackpoint->update($db,$pid,$tid,$prow['trackpoint_no'],
				$prow['elapsed_time'],$prow['x'],$prow['y'],$prow['z'],
				$prow['air_density']);
		}
		if ($trow['species_no'] == -1) {
			if ($prow['trackpoint_no'] == 0) {
				$z_height=$srow['anemometer_height'];
				$n=sscanf($trow['wind_direction'],"%d",$wind_direction);
				$wind_speed=$trow['wind_speed'];
				$x_speed=sin(deg2rad($wind_direction))*$wind_speed;
				$y_speed=cos(deg2rad($wind_direction))*$wind_speed;
			} else {
				$z_height=$prow['z'];
				$x_speed=($x_old-$prow['x'])/($prow['elapsed_time']-$time_old);
				$y_speed=($y_old-$prow['y'])/($prow['elapsed_time']-$time_old);
				$wind_speed=sqrt(pow($y_speed,2)+pow($x_speed,2));
				if (($x_speed >= 0) && ($y_speed) >= 0) {
					$wind_direction=rad2deg(asin($x_speed/$wind_speed));
				} elseif (($x_speed >= 0) && ($y_speed) < 0) {
					$wind_direction=180-rad2deg(asin($x_speed/$wind_speed));
				} elseif (($x_speed < 0) && ($y_speed) < 0) {
					$wind_direction=180-rad2deg(asin($x_speed/$wind_speed));
				} elseif (($x_speed < 0) && ($y_speed) >= 0) {
					$wind_direction=360+rad2deg(asin($x_speed/$wind_speed));
				}
			}
			$x_speed=round($x_speed,2,PHP_ROUND_HALF_EVEN);
			$y_speed=round($y_speed,2,PHP_ROUND_HALF_EVEN);
			$wind_direction=round($wind_direction,2,PHP_ROUND_HALF_EVEN);
			$wind_speed=round($wind_speed,2,PHP_ROUND_HALF_EVEN);
/*
echo $prow['trackpoint_no'].
	"	".$z_height.
	"	".$x_speed.
	"	".$y_speed.
	"	".$wind_direction.
	"	".$wind_speed.
	"</br>\n";
*/
			$y_old=$prow['y'];
			$x_old=$prow['x'];
			$time_old=$prow['elapsed_time'];
			$wdno=$prow['trackpoint_no'];
			$wdid=$winddata->select($db,$wpid,$wdno);
			if ($wdid == -1) {
//echo "Insert wind data: ";
				$wdid=$winddata->insert($db,$wpid,$wdno,
					$z_height,$x_speed,$y_speed,$wind_direction,$wind_speed);
//echo $wdid."</br>\n";
				$wdinum++;
			} else {
//echo "Update wind data: ".$wdid."</br>\n";
				$wdunum+=$winddata->update($db,$wdid,$wpid,$wdno,
					$z_height,$x_speed,$y_speed,$wind_direction,$wind_speed);
			}
		}
	}
	if ($trow['species_no'] == -1) {
		$wddres = $db->execute($sql_delwdata, array($wpid,$wdno));
		$wddnum += pg_affected_rows ($wddres);
	}
	$tdres = $db->execute($sql_delpoint, array($tid,$prow['trackpoint_no']));
	$tdnum += pg_affected_rows ($tdres);
}
$wddres = $db->execute($sql_delwinddata, array($sid,$wp_no));
$wddnum += pg_affected_rows ($wddres);
$wpdres = $db->execute($sql_delwindprofile, array($sid,$wp_no));
$wpdnum += pg_affected_rows ($wpdres);
$pdres = $db->execute($sql_deltrackpoint, array($sid,$file_id));
$pdnum += pg_affected_rows ($pdres);
$tdres = $db->execute($sql_deltrack, array($sid,$file_id));
$tdnum += pg_affected_rows ($tdres);

echo "Temporary database storage<br/>";
echo " - ".$tracknum." tracks read</br>";
echo "Track data<br/>";
if ($tinum > 0) echo " - ".$tinum." tracks inserted<br/>";
if ($tunum > 0) echo " - ".$tunum." tracks updated<br/>";
if ($tdnum > 0) echo " - ".$tdnum." tracks deleted<br/>";
echo "Trackpoint data<br/>";
if ($pinum > 0) echo " - ".$pinum." trackpoints inserted<br/>";
if ($punum > 0) echo " - ".$punum." trackpoints updated<br/>";
if ($pdnum > 0) echo " - ".$pdnum." trackpoints deleted<br/>";
echo "Wind profile data<br/>";
if ($wpinum > 0) echo " - ".$wpinum." wind profiles inserted<br/>";
if ($wpunum > 0) echo " - ".$wpunum." wind profiles updated<br/>";
if ($wpdnum > 0) echo " - ".$wpdnum." wind profiles deleted<br/>";
echo "Wind data<br/>";
if ($wdinum > 0) echo " - ".$wdinum." wind data records inserted<br/>";
if ($wdunum > 0) echo " - ".$wdunum." wind data records updated<br/>";
if ($wddnum > 0) echo " - ".$wddnum." wind data records deleted<br/>";

if (delete_import_orn_trackpoint($file_id)) {
	echo "Temporary database storage<br/>";
	echo " - ".$trow['track_no']." tracks deleted</br>";
}

return 0;
}
?>
