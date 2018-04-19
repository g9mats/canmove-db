<?php
/*
Creator: Mats J Svensson, CAnMove

This script loads GEN trackpoint data from staging area (l_gen_trackpoint) into
destination table (d_gen_trackpoint).
*/

function load_gen_trackpoint ($dataset_id,$file_id) {

require "./canmove.inc";

require_once $DBRoot."/action/delete_import_gen_trackpoint.php";

// Log on to database using common routine
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL statement that gets key values from file info
$sql_file="
select
	device_id,
	period,
	version,
	time_zone
from l_file
where file_id = $1
";

// SQL statement that delete records from d_gen_trackpoint
$sql_del="
delete from d_gen_trackpoint
where device_id = $2
  and period = $3
  and version = $4
  and log_time in (
	select log_time
	from d_gen_trackpoint
	where device_id = $2
	  and period = $3
	  and version = $4
	except
	select to_timestamp (log_time, 'YYYY-MM-DD HH24:MI')
	from l_gen_trackpoint
	where dataset_id = $1
	  and device_id = $2
	  and period = $3
	  and version = $4
)
";

// SQL statement that updates records in d_gen_trackpoint
$sql_upd="
update d_gen_trackpoint x
set (
	quality,
	latitude,
	longitude,
	speed,
	course,
	altitude
	) = (
	y.quality,
	cast (y.latitude as float),
	cast (y.longitude as float),
	cast (y.speed as float),
	cast (y.course as float),
	cast (y.altitude as float)
	)
from l_gen_trackpoint y
where y.device_id = x.device_id
  and y.period = x.period
  and y.version = x.version
  and to_timestamp (y.log_time, 'YYYY-MM-DD HH24:MI') = x.log_time
  and y.dataset_id = $1
  and y.device_id = $2
  and y.period = $3
  and y.version = $4
";

// SQL statement that inserts records into d_gen_trackpoint
$sql_ins="
insert into d_gen_trackpoint (
	device_id, period, version, log_time,
	quality, latitude, longitude,
	speed, course, altitude
	)
select
	device_id, period, version, to_timestamp (log_time, 'YYYY-MM-DD HH24:MI'),
	quality, cast(latitude as float), cast(longitude as float),
	cast(speed as float), cast(course as float), cast(altitude as float)
from l_gen_trackpoint
where dataset_id = $1
  and device_id = $2
  and period = $3
  and version = $4
  and to_timestamp (log_time, 'YYYY-MM-DD HH24:MI') in (
	select to_timestamp (log_time, 'YYYY-MM-DD HH24:MI')
	from l_gen_trackpoint
	where dataset_id = $1
	  and device_id = $2
	  and period = $3
	  and version = $4
	except
	select log_time
	from d_gen_trackpoint
	where device_id = $2
	  and period = $3
	  and version = $4
)
";

// Get key values for file
$res = $db->query($sql_file, array($file_id));
$device_id = $res[0]['device_id'];
$period = $res[0]['period'];
$version = $res[0]['version'];
$tz = $res[0]['time_zone'];
$sql_tz = "set time zone '".$tz."'";
$res = $db->execute($sql_tz);

// Synchronize a key combination
$res=$db->execute($sql_del,array($dataset_id,$device_id,$period,$version));
$res=$db->execute($sql_upd,array($dataset_id,$device_id,$period,$version));
$res=$db->execute($sql_ins,array($dataset_id,$device_id,$period,$version));

// Delete a key combination from staging area
delete_import_gen_trackpoint($dataset_id,$device_id,$period,$version);

return 0;
}
?>
