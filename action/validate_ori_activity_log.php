<?php
/*
Creator: Mats J Svensson, CAnMove

This script validates ORI activity log data in staging area
(l_ori_activity_log).

*/

function validate_ori_activity_log ($file_id) {

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
	version,
	original_name,
	time_zone
from l_file
where file_id = $1
";

// SQL statment that selects column info
$sql_selvar="
select
	p.header,
	r.data_type,
	r.nullable
from p_column p, r_data r
where p.data_id = r.data_id
  and p.dataset_id = $1
  and p.data_subset = 'activity_log'
order by p.order_no
";

// SQL statement that selects all rows from staging area
$sql_selstage="
select *
from l_ori_activity_log
where dataset_id = $1
  and version = $2
order by animal, capture_time, experiment_no, phase_no, time, order_no
";

// Initialize all counters
$row_count=0; $err_count=0;

$err_arr = array();

// Get key values for file
$res = $db->query($sql_file, array($file_id));
$dataset_id = $res[0]['dataset_id'];
$version = $res[0]['version'];
$file_name = $res[0]['original_name'];
$tz = $res[0]['time_zone'];
$sql_tz = "set time zone '".$tz."'";
$res = $db->execute($sql_tz);
$res = pg_query($DB,$sql_tz);

// Get all column information
$colinfo = $db->query($sql_selvar, array($dataset_id));

// Walk through all rows in l_ori_activity_log
$res = pg_query_params($DB, $sql_selstage, array($dataset_id,$version));
while ($row = pg_fetch_assoc($res)) {
	if ($row['order_no']==1)
		$row_count++;
	if (is_null($row['animal'])) {
		$err_arr[$err_count++]="Animal Id must not be null";
	}
	if (is_null($row['capture_time'])) {
		$err_arr[$err_count++]="Capture Time must not be null";
	}
	if (is_null($row['experiment_no'])) {
		$err_arr[$err_count++]="Experiment No must not be null";
	}
	if (is_null($row['phase_no'])) {
		$err_arr[$err_count++]="Phase No must not be null";
	}
	if (is_null($row['time'])) {
		$err_arr[$err_count++]="Time must not be null";
	}
	$col=$colinfo[$row['order_no']-1];
	$data=$row['data_value'];
	if (is_null($data)) {
		if (!$col['nullable'])
			$err_arr[$err_count++]=
				$row['animal'].",".
				$row['capture_time'].",".
				$row['experiment_no'].",".
				$row['phase_no'].",".
				$row['time'].": ".
				$col['header']." must not be null";
		continue;
	}
	switch($col['data_type']) {
		case "boolean":
			if (strpos("|YES|NO|","|".$data."|")===false)
				$err_arr[$err_count++]=
					$row['animal'].",".
					$row['capture_time'].",".
					$row['experiment_no'].",".
					$row['phase_no'].",".
					$row['time'].": ".
					$col['header'].': "'.
					$data.'" is not YES or NO';
			break;
		case "float":
			if (!is_numeric($data)) 
				$err_arr[$err_count++]=
					$row['animal'].",".
					$row['capture_time'].",".
					$row['experiment_no'].",".
					$row['phase_no'].",".
					$row['time'].": ".
					$col['header'].': "'.
					$data.'" is not a decimal number';
			break;
		case "integer":
			if (!is_numeric($data)) 
				$err_arr[$err_count++]=
					$row['animal'].",".
					$row['capture_time'].",".
					$row['experiment_no'].",".
					$row['phase_no'].",".
					$row['time'].": ".
					$col['header'].': "'.
					$data.'" is not an integer';
			else
				if (strpos($data,".")) 
					$err_arr[$err_count++]=
						$row['animal'].",".
						$row['capture_time'].",".
						$row['experiment_no'].",".
						$row['phase_no'].",".
						$row['time'].": ".
						$col['header'].': "'.
						$data.'" is not a whole number';
			break;
		default:
			break;
	}
}

pg_free_result($res);

echo "File: ".$file_name."<br/>";
echo $row_count." rows read<br/>";
echo $err_count." errors found<br/>";
for ($i=0; $i<min(20,$err_count); $i++)
	echo $err_arr[$i]."<br/>";
if (count($err_arr)>20)
	echo "...<br/>";

return $err_count;

}

?>
