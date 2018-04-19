<?php
/*
Creator: Mats J Svensson, CAnMove

This script validates ORI estimation data in staging area (l_ori_estimation).
*/

function validate_ori_estimation ($file_id) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL statement that gets key values from file info
$sql_file="
select
	dataset_id,
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
  and p.data_subset = 'estimation'
order by p.order_no
";

// SQL statement that selects all rows from staging area
$sql_selstage="
select * from l_ori_estimation
where dataset_id = $1
  and version = $2
order by animal, capture_time, experiment_no, phase_no
";

// SQL statment that translate operator names to id
$sql_updoper="
update l_ori_estimation l
set operator_id = r.person_id
from r_person r
where r.first_name||' '||r.last_name = l.operator_id
  and l.dataset_id = $1
  and l.version = $2
";

// Initialize all counters
$row_count=0; $err_count=0;

$err_arr = array();

// Get key values for file
$res = $db->query($sql_file, array($file_id));
$dataset_id = $res[0]['dataset_id'];
$version = $res[0]['version'];
$tz = $res[0]['time_zone'];
$sql_tz = "set time zone '".$tz."'";
$res = $db->execute($sql_tz);

// Get all column information
$colinfo = $db->query($sql_selvar, array($dataset_id));

// Translate any operator name to id
$db->execute($sql_updoper,array($dataset_id,$version));

// Walk through all rows in l_ori_estimation
$stage = $db->query($sql_selstage, array($dataset_id,$version));
foreach ($stage as $row) {
	$row_count++;
	foreach ($colinfo as $col) {
		$data=$row[$col['load_name']];
		if (is_null($data)) {
			if (!$col['nullable'])
				$err_arr[$err_count++]=$row['animal'].": ".
					$col['header']." must not be null";
			continue;
		}
		switch($col['data_type']) {
			case "boolean":
				if (strpos("|YES|NO|","|".$data."|")===false)
					$err_arr[$err_count++]=$row['animal'].": ".
						$col['header'].': "'.
						$data.'" is not YES or NO';
				break;
			case "float":
				if (!is_numeric($data)) 
					$err_arr[$err_count++]=$row['animal'].": ".
						$col['header'].': "'.
						$data.'" is not a decimal number';
				break;
			case "integer":
				if (!is_numeric($data)) 
					$err_arr[$err_count++]=$row['animal'].": ".
						$col['header'].': "'.
						$data.'" is not an integer';
				else
					if (strpos($data,".")) 
						$err_arr[$err_count++]=$row['animal'].": ".
							$col['header'].': "'.
							$data.'" is not a whole number';
				break;
			default:
				break;
		}
	}
}

echo "<hr/>";
echo $row_count." rows read<br/>";
echo $err_count." errors found<br/>";
for ($i=0; $i<min(20,$err_count); $i++)
	echo $err_arr[$i]."<br/>";

return $err_count;

}
?>
