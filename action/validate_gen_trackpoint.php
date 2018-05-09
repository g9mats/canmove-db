<?php
/*
Creator: Mats J Svensson, CAnMove

This script validates GEN trackpoint data in staging area (l_gen_trackpoint).
*/

function validate_gen_trackpoint ($file_id) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Log on to database using simple routine
require $DBRoot."/lib/DB.php";

// SQL statement that gets key values from file info
$sql_file="
select
	dataset_id,
	device_id,
	period,
	version,
	time_zone
from l_file
where file_id = $1
";

// SQL statment that selects column info
$sql_selvar="
select
	p.load_name,
	p.header,
	r.column_name,
	r.data_type,
	r.mandatory,
	r.nullable
from p_column p, r_data r
where p.data_id = r.data_id
  and p.dataset_id = $1
  and p.data_subset = 'trackpoint'
order by p.order_no
";

// SQL statement that selects all rows from staging area
$sql_selstage="
select *
from l_gen_trackpoint
where dataset_id = $1
  and device_id = $2
  and period = $3
  and version = $4
order by log_time
";

// SQL statement that selects track info
$sql_seltrack="
select a.animal, d.device
from d_gen_animal a, d_gen_track t, d_gen_device d
where a.animal_id = t.animal_id
  and t.track_id = d.track_id
  and a.dataset_id = $1
  and d.device_id = $2
";

// Initialize all counters
$row_count=0; $err_count=0;

$err_arr = array();

// Get key values for file
$res = $db->query($sql_file, array($file_id));
$dataset_id = $res[0]['dataset_id'];
$device_id = $res[0]['device_id'];
$period = $res[0]['period'];
$version = $res[0]['version'];
$tz = $res[0]['time_zone'];
$sql_tz = "set time zone '".$tz."'";
$res = $db->execute($sql_tz);
$res = pg_query($DB,$sql_tz);

// Get all column information
$colinfo = $db->query($sql_selvar, array($dataset_id));

// Walk through all rows in l_gen_trackpoint
$res = pg_query_params($DB, $sql_selstage,
			array($dataset_id,$device_id,$period,$version));
while ($row = pg_fetch_assoc($res)) {
	$row_count++;
	foreach ($colinfo as $col) {
		$data=$row[$col['load_name']];
		if (is_null($data)) {
			if (!$col['nullable'])
				$err_arr[$err_count++]=
					$row['log_time'].": ".
					$col['header']." must not be null";
			continue;
		}
		switch($col['data_type']) {
			case "float":
				if (!is_numeric($row[$col['load_name']])) 
					$err_arr[$err_count++]=
						$row['log_time'].": ".
						$col['header'].': "'.
						$data.'" is not a decimal number';
				break;
			case "integer":
				if (!is_numeric($row[$col['load_name']])) 
					$err_arr[$err_count++]=
						$row['log_time'].": ".
						$col['header'].': "'.
						$data.'" is not an integer';
				else
					if (strpos($row[$col['load_name']],".")) 
						$err_arr[$err_count++]=
							$row['log_time'].": ".
							$col['header'].': "'.
							$data.'" is not a whole number';
				break;
			default:
				break;
		}
	}
}

pg_free_result($res);

$track = $db->query($sql_seltrack, array($dataset_id,$device_id));
echo "<hr/>";
echo "Track: ".$track[0]['animal']." - ".$track[0]['device']."<br/>";
echo " - ".$row_count." rows read<br/>";
echo " - ".$err_count." errors found<br/>";
for ($i=0; $i<min(20,$err_count); $i++)
	echo $err_arr[$i]."<br/>";
if (count($err_arr)>20)
	echo "...<br/>";

return $err_count;

}

?>
