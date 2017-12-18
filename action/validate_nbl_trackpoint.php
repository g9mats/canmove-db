<?php
/*
Creator: Mats J Svensson, CAnMove

This script validates NBL trackpoint data in staging area (l_nbl_trackpoint).
*/

function validate_nbl_trackpoint ($file_id) {

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
	dataset_id
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

// SQL statement that changes all NA-values to null
$sql_null="
update l_nbl_trackpoint
set COLUMN=null
where dataset_id = $1
  and COLUMN='NA'
";

// SQL statement that selects all rows from staging area
$sql_selstage="
select *
from l_nbl_trackpoint
where dataset_id = $1
order by recording, track, time
";

// Initialize all counters
$row_count=0; $err_count=0;

$err_arr = array();

// Get key values for file
$res = $db->query($sql_file, array($file_id));
$dataset_id = $res[0]['dataset_id'];

// Get all column information
$colinfo = $db->query($sql_selvar, array($dataset_id));

// Change all NA-values to null
$sql_temp=str_replace("COLUMN","x",$sql_null);
$res = $db->execute($sql_temp, array($dataset_id));
$sql_temp=str_replace("COLUMN","y",$sql_null);
$res = $db->execute($sql_temp, array($dataset_id));
$sql_temp=str_replace("COLUMN","z",$sql_null);
$res = $db->execute($sql_temp, array($dataset_id));

// Walk through all rows in l_nbl_trackpoint
$res = pg_query_params($DB, $sql_selstage, array($dataset_id));
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

echo " - ".$row_count." rows read<br/>";
echo " - ".$err_count." errors found<br/>";
for ($i=0; $i<min(20,$err_count); $i++)
	echo $err_arr[$i]."<br/>";
if (count($err_arr)>10)
	echo "...<br/>";

return $err_count;

}

?>
