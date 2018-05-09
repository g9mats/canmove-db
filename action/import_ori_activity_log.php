<?php
/*
Creator: Mats J Svensson, CAnMove

This script loads ORI data from tab separated text file into staging area.

The first row should contain agreed headers defined in table r_data but
do not need to appear in a certain order. The column information is loaded
into l_column. If an unknown header is found the column will be ignored.

The actual Activity Log data is loaded into table l_ori_activity_log for
subsequent validation.
*/

function import_ori_activity_log ($fname, $separator, $file_id) {
if ($separator=="tab")
	$schar="	";
else
	$schar=",";
ini_set("auto_detect_line_endings", true);

require "./canmove.inc";

// Open the file and read the first line with headers
// Use a filter to deal with strange characters
// Leave file open for actual data loading
require_once $DBRoot."/lib/UTF8EncodeFilter.php";
$lfile = fopen ($fname, 'r');
stream_filter_prepend($lfile, "utf8encode"); 
$buffer = fgets ($lfile);
$buffer = rtrim ($buffer, "\t\r\n");
$headers = explode ($schar, $buffer);

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

// SQL statement that deletes any previously loaded header definitions
$sql_delvar="
delete from l_column
where dataset_id = $1
  and data_subset = 'activity_log'
";

// SQL statment that selects a column definition
$sql_selvar="
select
	data_id,
	load_name,
	column_type,
	data_type,
	case_type,
	header
from r_data
where storage_type = 'ORI'
  and data_subset = 'activity_log'
  and header = $1
union
select
	d.data_id,
	d.load_name,
	d.column_type,
	d.data_type,
	d.case_type,
	case when a.keep_alias
		then a.header
		else d.header
	end
from r_data d, r_data_alias a
where d.data_id = a.data_id
  and d.storage_type = 'ORI'
  and d.data_subset = 'activity_log'
  and a.header = $1
";

// SQL statment that inserts one column definition
$sql_insvar="
insert into l_column (
	dataset_id,
	data_subset,
	order_no,
	data_id,
	load_name,
	header
) values ($1, $2, $3, $4, $5, $6)
";

// SQL statement that checks for previously loaded column definitions in project table
$sql_colnum="
select count(*) colnum
from p_column
where dataset_id = $1
  and data_subset = 'activity_log'
";

// SQL statement that inserts loaded column definitions into project table
$sql_colins="
insert into p_column (
	dataset_id,
	data_subset,
	order_no,
	data_id,
	load_name,
	header
	)
select
	dataset_id,
	data_subset,
	order_no,
	data_id,
	load_name,
	header
from l_column
where dataset_id = $1
  and data_subset = 'activity_log'
order by order_no
";

// SQL statement that checks for differences from previously loaded column definitions in project table
$sql_coldiff="
select header, 'missing' status
from p_column
where dataset_id = $1
  and data_subset = 'activity_log'
except
select header, 'missing' status
from l_column
where dataset_id = $1
  and data_subset = 'activity_log'
union
select header, 'unknown' status
from l_column
where dataset_id = $1
  and data_subset = 'activity_log'
except
select header, 'unknown' status
from p_column
where dataset_id = $1
  and data_subset = 'activity_log'
order by header
";

// SQL statement that deletes any previously loaded data
$sql_deldata="
delete from l_ori_activity_log
where dataset_id = $1
  and version = $2
";

// SQL statment that inserts a data value
$sql_insdata="
insert into l_ori_activity_log (
	dataset_id,
	animal,
	capture_time,
	experiment_no,
	phase_no,
	version,
	order_no,
	data_id,
	time,
	data_value
) values ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10)
";

// Get key values for file
$res = $db->query($sql_file, array($file_id));
$dataset_id = $res[0]['dataset_id'];
$version = $res[0]['version'];
$tz = $res[0]['time_zone'];
$sql_tz = "set time zone '".$tz."'";
$res = $db->execute($sql_tz);

// Delete any old column definitions
$res=$db->execute($sql_delvar,array($dataset_id));
//echo pg_affected_rows($result)." column definitions deleted<br/>";

// Delete any previously loaded data from l_ori_activity_log
$res=$db->execute($sql_deldata,
			array($dataset_id,$version));

$colnum=0;		// Count columns in text file
$loadcolnum=0;	// Count columns to load in text file
// Keep track of data type and any case restrictions in an array
$colinfo=array("data_id"=>array(),"data_type"=>array(),"case_type"=>array());
// Keep track of column references for loading in an array
// They are less than or equal to total number of columns
$loadindex=array();
$animal_idx=-1;
$captime_idx=-1;
$expno_idx=-1;
$phaseno_idx=-1;
$time_idx=-1;
// Walk through all headers
foreach ($headers as $htext) {
	// Fetch a column definition for current header
	if (!$res=$db->query($sql_selvar,array($htext))) {
		// Continue if header definition is not found
		$colnum++;
		continue;
	}
	if ($htext == "Animal Id") {
		// Save column index and continue if header is animal id
		$animal_idx=$colnum;
		$colnum++;
		continue;
	} elseif ($htext == "Capture Time") {
		// Save column index and continue if header is capture time
		$captime_idx=$colnum;
		$colnum++;
		continue;
	} elseif ($htext == "Experiment No") {
		// Save column index and continue if header is experiment no
		$expno_idx=$colnum;
		$colnum++;
		continue;
	} elseif ($htext == "Phase No") {
		// Save column index and continue if header is phase no
		$phaseno_idx=$colnum;
		$colnum++;
		continue;
	} elseif ($htext == "Time") {
		// Save column index and continue if header is time
		$time_idx=$colnum;
		$colnum++;
		continue;
	}
	$row=$res[0];
	// Save column information for later use
	$colinfo['data_id'][$loadcolnum]=$row['data_id'];
	$colinfo['data_type'][$loadcolnum]=$row['data_type'];
	$colinfo['case_type'][$loadcolnum]=$row['case_type'];
	$loadindex[$loadcolnum++]=$colnum++;
	// Insert one column definition in table l_column
	$res=$db->execute($sql_insvar,
		array(
			$dataset_id,
			'activity_log',
			$loadcolnum,
			$row['data_id'],
			$row['load_name'],
			$row['header']
		)
	);
}
if ($animal_idx == -1) {
	echo "Error: File must contain Animal Id column<br/>";
	return 1;
} elseif ($captime_idx == -1) {
	echo "Error: File must contain Capture Time column<br/>";
	return 1;
} elseif ($time_idx == -1) {
	echo "Error: File must contain Time column<br/>";
	return 1;
}
$keycolnum=5;
if ($expno_idx == -1) {$experiment_no=1; $keycolnum-=1;}
if ($phaseno_idx == -1) {$phase_no=1; $keycolnum-=1;}
echo " - ".($loadcolnum+$keycolnum)." column definitions imported<br/>";

// Check for differences from previously loaded column definitions
$res=$db->query($sql_colnum,array($dataset_id));
$row=$res[0];
if ($row['colnum']==0) {
	$res=$db->execute($sql_colins,array($dataset_id));
} else {
	if ($res=$db->query($sql_coldiff,array($dataset_id))) {
		foreach ($res as $row)
			echo "Column ".$row['header']." is ".$row['status']."<br/>";
		return 1;
	}
}

$datanum=0;			// Keep track of number of data rows loaded
while (($buffer = fgets ($lfile)) !== FALSE) { // Read rest of file
	$buffer = rtrim ($buffer, "\t\r\n");
	$data = array_pad (explode ($schar, $buffer), $colnum, "");
	$animal=trim($data[$animal_idx]);
	$capture_time=trim($data[$captime_idx]);
	if ($expno_idx != -1) $experiment_no=trim($data[$expno_idx]);
	if ($phaseno_idx != -1) $phase_no=trim($data[$phaseno_idx]);
	$time=trim($data[$time_idx]);
	$notnull=0;
	for ($i = 0; $i < $loadcolnum; $i++) {	// Walk through all columns
		$li=$loadindex[$i];
		$data[$li]=trim($data[$li]);	// Trim spaces
		if ($data[$li]=="")
			$data[$li]=NULL;		// Convert empty string to NULL
		else {
			$notnull++;
			// Switch "," to "." for float
			if ($colinfo['data_type'][$i]=="float")
				$data[$li]=str_replace(",", ".", $data[$li]);
			// Execute case restriction
			if ($colinfo['case_type'][$i]=="upper")
				$data[$li]=strtoupper($data[$li]);
			if ($colinfo['case_type'][$i]=="lower")
				$data[$li]=strtolower($data[$li]);
		}
	}
	if ($notnull > 0) {
		// Insert one data row into table l_ori_activity_log for every variable
		for ($i = 0; $i < $loadcolnum; $i++) {
			$res=$db->execute($sql_insdata,
				array(
					$dataset_id,
					$animal,
					$capture_time,
					$experiment_no,
					$phase_no,
					$version,
					$i+1,
					$colinfo['data_id'][$i],
					$time,
					$data[$loadindex[$i]]
				)
			);
		}
		$datanum++;
	}
}
fclose ($lfile);

// Delete column definitions
$res=$db->execute($sql_delvar,array($dataset_id));

echo " - ".$datanum." data rows imported<br/>";

return 0;
}
?>
