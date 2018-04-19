<?php
/*
Creator: Mats J Svensson, CAnMove

This script validates ORN trackpoint data in staging area (l_orn_location,
l_orn_session, l_orn_track and l_orn_trackpoint).
*/

function validate_orn_trackpoint ($file_id) {

require "./canmove.inc";

// Log on to database using common routine
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL statement that gets key values from file info
$sql_file="
select
	dataset_id,
	original_name,
	time_zone
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

// SQL statements that selects location data from database
$sql_selloc2="
select * from r_orn_location
where location = $1
";

// SQL statment that gets any existing taxon replacement
$sql_selrep="
select
	new_taxon
from r_orn_taxon_replace
where old_taxon = $1
";

// SQL statment that gets the ITIS tsn for a taxon
$sql_seltsn="
select
	tsn,
	name_usage
from r_taxon
where complete_name = $1
order by name_usage desc
";

// SQL statment that gets a recommended valid synonym for an invalid taxon
$sql_tsnsyn="
select
	t.complete_name
from r_taxon_synonym s, r_taxon t
where s.tsn_accepted = t.tsn
  and s.tsn = $1
";

// SQL statment that gets the Swedish name with correct character set
$sql_selname="
select
	english_name,
	swedish_name
from r_orn_taxon
where species_no = $1
";

// SQL statment that updates the taxon information in l_orn_track
$sql_updtaxon="
update l_orn_track
set itis_tsn = $3,
	taxon = $4,
	english_name = $5,
	swedish_name = $6
where file_id = $1
  and track_no = $2
";

// Initialize all counters
$row_count=0; $err_count=0;

$err_arr = array();

// Get key values for file
$res = $db->query($sql_file, array($file_id));
$dataset_id = $res[0]['dataset_id'];
$file_name = $res[0]['original_name'];
$tz = $res[0]['time_zone'];
$sql_tz = "set time zone '".$tz."'";
$res = $db->execute($sql_tz);
echo "File: ".$file_name."</br>";

// Check for previous use of this location
$res = $db->query($sql_selloc, array($file_id));
if ($res2 = $db->query($sql_selloc2, array($res[0]['location']))) {
	$r=$res[0];
	$r2=$res2[0];
	if (($r['latitude'] != $r2['latitude']) or
		($r['longitude'] != $r2['longitude']) or
		($r['altitude'] != $r2['altitude']) or
		($r['declination'] != $r2['declination'])) {
		echo "Warning: Location ".$res[0]['location'].
			" found in database with diviant values.</br>";
		if ($r['latitude'] != $r2['latitude'])
			echo " - Latitude: ".$r2['latitude']."</br>";
		if ($r['longitude'] != $r2['longitude'])
			echo " - Longitude: ".$r2['longitude']."</br>";
		if ($r['altitude'] != $r2['altitude'])
			echo " - Altitude: ".$r2['altitude']."</br>";
		if ($r['declination'] != $r2['declination'])
			echo " - Declination: ".$r2['declination']."</br>";
	}
}

// Walk through all tracks in l_orn_track
$res = $db->query($sql_seltrack, array($file_id));
foreach ($res as $row) {
	$row_count++;
	if ($repres=$db->query($sql_selrep, array($row['taxon']))) {
		$taxon=$repres[0]['new_taxon'];
		echo "Run ".$row['track_no']." information: Taxon ".$row['taxon'].
				" has been replaced.</br>";
		echo "................ Changed taxon to ".$taxon.".</br>";
	} else {
		$taxon=$row['taxon'];
	}
	if ($row['species_no'] == -1) {
		// Take care of balloon track
		$ures=$db->execute($sql_updtaxon,
				array($file_id,$row['track_no'],-1,$taxon,"Balloon","Ballong"));
	} elseif ($tres=$db->query($sql_seltsn, array($taxon))) {
		if ($nameres=$db->query($sql_selname, array($row['species_no']))) {
			$engname=$nameres[0]['english_name'];
			$swename=$nameres[0]['swedish_name'];
		} else {
			$engname=$row['english_name'];
			$swename=$row['swedish_name'];
		}
		$t=$tres[0];
		$ures=$db->execute($sql_updtaxon,array($file_id,$row['track_no'],
					$t['tsn'],$taxon,$engname,$swename));
		if ($t['name_usage'] == "invalid") {
			echo "Run ".$row['track_no']." warning: Taxon ".$taxon.
				" is marked invalid in ITIS.</br>";
			if ($sres=$db->query($sql_tsnsyn, array($t['tsn']))) {
				echo "................ Recommended synonym is ".
					$sres[0]['complete_name'].".</br>";
			}
		}
	} else {
		$err_arr[$err_count++]="Run ".$row['track_no'].
				" ERROR: Could not find Taxon: ".$taxon;
	}
}

echo $row_count." tracks read<br/>";
echo $err_count." errors found<br/>";
for ($i=0; $i<min(20,$err_count); $i++)
	echo $err_arr[$i]."<br/>";

return $err_count;

}
?>
