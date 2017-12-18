<?php
/*
Creator: Mats J Svensson, CAnMove

This script loads ORN trackpoint data from tab separated text file into the tables l_orn_location, l_orn_session, l_orn_track and l_orn_trackpoint for subsequent validation.
*/

function import_orn_trackpoint ($fname, $separator, $file_id) {
/*
if ($separator=="tab")
	$schar="	";
else
	$schar=",";
*/
$schar="	"; // Separator character must be tab since comma occurs in comment

ini_set("auto_detect_line_endings", true);

require "./canmove.inc";

// Log on to database using common routine
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL statement that gets key values from file info
$sql_file="
select
	dataset_id
from l_file
where file_id = $1
";

// SQL statements that deletes any previously loaded data
$sql_delloc="
delete from l_orn_location
where file_id = $1
";
$sql_delsess="
delete from l_orn_session
where file_id = $1
";
$sql_deltrack="
delete from l_orn_track
where file_id = $1
";
$sql_delpoint="
delete from l_orn_trackpoint
where file_id = $1
";

// SQL statement that inserts location data into staging table
$sql_insloc="
insert into l_orn_location (
	file_id,
	dataset_id,
	location,
	latitude,
	longitude,
	altitude,
	declination
	) values ($1, $2, $3, $4, $5, $6, $7)
";

// SQL statement that inserts session data into staging table
$sql_inssess="
insert into l_orn_session (
	file_id,
	dataset_id,
	session_time,
	height_datum,
	height_source,
	wind_source,
	anemometer_height,
	taxa_file,
	activity_file
	) values ($1, $2, $3, $4, $5, $6, $7, $8, $9)
";

// SQL statement that inserts track data into staging table
$sql_instrack="
insert into l_orn_track (
	file_id,
	track_no,
	dataset_id,
	start_time,
	taxon,
	species_no,
	english_name,
	swedish_name,
	wind_direction,
	wind_speed,
	barometer,
	temperature,
	quantity,
	sex,
	age,
	crop,
	flight_style,
	activity,
	remark
	) values ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10,
		$11, $12, $13, $14, $15, $16, $17, $18, $19)
";

// SQL statement that inserts trackpoint data into staging table
$sql_inspoint="
insert into l_orn_trackpoint (
	file_id,
	track_no,
	trackpoint_no,
	dataset_id,
	elapsed_time,
	x,
	y,
	z,
	air_density
	) values ($1, $2, $3, $4, $5, $6, $7, $8, $9)
";

// Get key values for file
$res = $db->query($sql_file, array($file_id));
$dataset_id = $res[0]['dataset_id'];

// Delete any previously loaded data from staging area
$res=$db->execute($sql_delloc,array($file_id));
$res=$db->execute($sql_delsess,array($file_id));
$res=$db->execute($sql_deltrack,array($file_id));
$res=$db->execute($sql_delpoint,array($file_id));

// Open the file
// Use a filter to deal with strange characters
require_once $DBRoot."/lib/UTF8EncodeFilter.php";
$lfile = fopen ($fname, 'r');
stream_filter_prepend($lfile, "utf8encode"); 

$status="location";
$tracknum=0;
while (($buffer = fgets ($lfile)) !== FALSE) { // Read the file
	$buffer = rtrim ($buffer, "\r\n");
	$data = explode ($schar, $buffer);

	if ($status == "location") {
		if ($data[0] == "Date") {
			$date = $data[1];
//echo "date=".$date."</br>\n";
		} elseif ($data[0] == "Site") {
			$location = $data[1];
		} elseif ($data[0] == "Latitude") {
			$latitude = trim($data[1]);
		} elseif ($data[0] == "Longitude") {
			$longitude = trim($data[1]);
		} elseif ($data[0] == "Elevation") {
			$altitude = trim($data[1]);
		} elseif ($data[0] == "Compass Variation") {
			$declination = trim($data[1]);
			$res=$db->execute($sql_insloc,
				array($file_id,$dataset_id,
					$location,$latitude,$longitude,
					$altitude,$declination));
			$status = "session";
		}
	} elseif ($status == "session") {
		if ($data[0] == "Height datum") {
			$height_datum = $data[1];
			$height_source = $data[2];
		} elseif ($data[0] == "Wind source") {
			$wind_source = $data[1];
		} elseif ($data[0] == "Anemometer Height") {
			$anemometer_height = $data[1];
		} elseif ($data[0] == "Species names file") {
			$taxa_file = $data[1];
		} elseif ($data[0] == "Activity names file") {
			$activity_file = $data[1];
			$status="track";
		}
	} elseif ($status == "track") {
		if ($data[0] == "Run") {
			$track_no = trim($data[1]);
		} elseif (($data[0] == "Time") && ($data[1] != "X")) {
			$time = $data[1];
			$start_time = $date." ".$time;
//echo "start_time=".$start_time."</br>\n";
			if ($track_no == 1) {
				$res=$db->execute($sql_inssess,
					array($file_id,$dataset_id,$start_time,
						$height_datum,$height_source,$wind_source,
						$anemometer_height,$taxa_file,$activity_file));
				}
		} elseif ($data[0] == "Species") {
			$species_no = $data[1];
			$taxon = $data[2];
			if (count($data)>3)
				$english_name = $data[3];
			else
				$english_name = "";
			if (count($data)>4)
				$swedish_name = $data[4];
			else
				$swedish_name = "";
			if ($species_no == -1) {
				$sex="-1";
				$age="-1";
				$crop="-1";
				$flight_style="-1";
				$activity="-1";
			}
		} elseif ($data[0] == "Wind Dir from") {
			$wind_direction = $data[1];
		} elseif ($data[0] == "Wind speed") {
			$wind_speed = $data[1];
		} elseif ($data[0] == "Baro pressure") {
			$barometer = $data[1];
		} elseif ($data[0] == "Air Temperature") {
			$temperature = $data[1];
		} elseif ($data[0] == "Comment:") {
			$comment = explode (",", $data[1]);
			if (count($comment)>1 && is_numeric($comment[0])) {
				$quantity = $comment[0];
				$remark = trim(substr($data[1],strpos($data[1],",")+1));
			} elseif (is_numeric($data[1])) {
				$quantity = $data[1];
				$remark = "";
			} else {
				$quantity = -1;
				$remark = trim($data[1]);
			}
		} elseif ($data[0] == "Sex") {
			$sex = trim($data[1]);
		} elseif ($data[0] == "Age") {
			$age = trim($data[1]);
		} elseif ($data[0] == "Crop") {
			$crop = trim($data[1]);
		} elseif ($data[0] == "Flight style") {
			$flight_style = trim($data[1]);
		} elseif ($data[0] == "Activity") {
			$activity = trim($data[1]);
		} elseif (($data[0] == "Time") && ($data[1] == "X")) {
			$res=$db->execute($sql_instrack,
				array($file_id,$track_no,$dataset_id,$start_time,
				$taxon,$species_no,$english_name,$swedish_name,
				$wind_direction,$wind_speed,$barometer,$temperature,
				$quantity,$sex,$age,$crop,$flight_style,$activity,$remark));
			$tracknum++;
			$trackpoint_no=0;
		} elseif (is_numeric($data[0])) {
			$elapsed_time = $data[0];
			$x = $data[1];
			$y = $data[2];
			$z = $data[3];
			$air_density = $data[4];
			$res=$db->execute($sql_inspoint,
				array($file_id,$track_no,$trackpoint_no,$dataset_id,
				$elapsed_time,$x,$y,$z,$air_density));
			$trackpoint_no++;
		} else {
			echo "Unknown label: ".$data[0].". Exiting ...";
			return(1);
		}
	}
}
fclose ($lfile);

echo " - ".$tracknum." tracks imported<br/>";

return 0;
}
?>
