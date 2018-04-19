<?php
/*
Creator: Mats J Svensson, CAnMove

This script loads GEN data from tab separated text file into staging area.

The first row should contain agreed headers defined in table r_data but
do not need to appear in a certain order. The column information is loaded
into l_column. If an unknown header is found the column will be ignored.

The actual Datapoint data is loaded into table l_gen_datapoint for
subsequent validation.
*/

function import_gen_datapoint ($fname, $separator, $file_id) {
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
	device_id,
	period,
	version,
	varset,
	time_zone
from l_file
where file_id = $1
";

// SQL statement that deletes any previously loaded header definitions
$sql_delvar="
delete from l_column
where dataset_id = $1
  and data_subset = 'datapoint'
  and varset = $2
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
where storage_type = 'GEN'
  and data_subset = 'datapoint'
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
  and d.storage_type = 'GEN'
  and d.data_subset = 'datapoint'
  and a.header = $1
";

// SQL statment that inserts one column definition
$sql_insvar="
insert into l_column (
	dataset_id,
	data_subset,
	varset,
	order_no,
	data_id,
	load_name,
	header
) values ($1, $2, $3, $4, $5, $6, $7)
";

// SQL statement that checks for previously loaded column definitions in project table
$sql_colnum="
select count(*) colnum
from p_column
where dataset_id = $1
  and data_subset = 'datapoint'
  and varset = $2
";

// SQL statement that inserts loaded column definitions into project table
$sql_colins="
insert into p_column (
	dataset_id,
	data_subset,
	varset,
	order_no,
	data_id,
	load_name,
	header
	)
select
	dataset_id,
	data_subset,
	varset,
	order_no,
	data_id,
	load_name,
	header
from l_column
where dataset_id = $1
  and data_subset = 'datapoint'
  and varset = $2
order by order_no
";

// SQL statement that checks for differences from previously loaded column definitions in project table
$sql_coldiff="
select header, 'missing' status
from p_column
where dataset_id = $1
  and data_subset = 'datapoint'
  and varset = $2
except
select header, 'missing' status
from l_column
where dataset_id = $1
  and data_subset = 'datapoint'
  and varset = $2
union
select header, 'unknown' status
from l_column
where dataset_id = $1
  and data_subset = 'datapoint'
  and varset = $2
except
select header, 'unknown' status
from p_column
where dataset_id = $1
  and data_subset = 'datapoint'
  and varset = $2
order by header
";

// SQL statement that deletes any previously loaded data
$sql_deldata="
delete from l_gen_datapoint
where dataset_id = $1
  and device_id = $2
  and period = $3
  and version = $4
  and varset = $5
";

// SQL statment that inserts a data value
$sql_insdata="
insert into l_gen_datapoint (
	dataset_id,
	device_id,
	period,
	version,
	varset,
	order_no,
	data_id,
	log_time,
	data_value
) values ($1,$2,$3,$4,$5,$6,$7,$8,$9)
";

// Get key values for file
$res = $db->query($sql_file, array($file_id));
$dataset_id = $res[0]['dataset_id'];
$device_id = $res[0]['device_id'];
$period = $res[0]['period'];
$version = $res[0]['version'];
$varset = $res[0]['varset'];
$tz = $res[0]['time_zone'];
$sql_tz = "set time zone '".$tz."'";
$res = $db->execute($sql_tz);

// Delete any old column definitions
$res=$db->execute($sql_delvar,array($dataset_id,$varset));
//echo pg_affected_rows($result)." column definitions deleted<br/>";

// Delete any previously loaded data from l_gen_datapoint
$res=$db->execute($sql_deldata,
			array($dataset_id,$device_id,$period,$version,$varset));

$colnum=0;		// Count columns in text file
$loadcolnum=0;	// Count columns to load in text file
// Keep track of data type and any case restrictions in an array
$colinfo=array("data_id"=>array(),"data_type"=>array(),"case_type"=>array());
// Keep track of column references for loading in an array
// They are less than or equal to total number of columns
$loadindex=array();
$log_time_idx=-1;
// Walk through all headers
foreach ($headers as $htext) {
	// Fetch a column definition for current header
	if (!$res=$db->query($sql_selvar,array($htext))) {
		// Continue if header definition is not found
		$colnum++;
		continue;
	}
	if ($htext == "Log Time") {
		// Save column index and continue if header is log_time
		$log_time_idx=$colnum;
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
			'datapoint',
			$varset,
			$loadcolnum,
			$row['data_id'],
			$row['load_name'],
			$row['header']
		)
	);
}
if ($log_time_idx == -1) {
	echo "Error: File must contain Log Time column<br/>";
	return 1;
} else
	echo " - ".($loadcolnum+1)." column definitions imported<br/>";

// Check for differences from previously loaded column definitions
$res=$db->query($sql_colnum,array($dataset_id,$varset));
$row=$res[0];
if ($row['colnum']==0) {
	$res=$db->execute($sql_colins,array($dataset_id,$varset));
} else {
	if ($res=$db->query($sql_coldiff,array($dataset_id,$varset))) {
		foreach ($res as $row)
			echo "Column ".$row['header']." is ".$row['status']."<br/>";
		return 1;
	}
}

$datanum=0;			// Keep track of number of data rows loaded
while (($buffer = fgets ($lfile)) !== FALSE) { // Read rest of file
	$buffer = rtrim ($buffer, "\t\r\n");
	$data = array_pad (explode ($schar, $buffer), $colnum, "");
	$data[$log_time_idx]=trim($data[$log_time_idx]);	// Trim spaces
	if ($data[$log_time_idx]=="")		// Skip rows with no log_time
		continue;
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
		// Insert one data row into table l_gen_datapoint for every variable
		for ($i = 0; $i < $loadcolnum; $i++) {
			$res=$db->execute($sql_insdata,
				array(
					$dataset_id,
					$device_id,
					$period,
					$version,
					$varset,
					$i+1,
					$colinfo['data_id'][$i],
					$data[$log_time_idx],
					$data[$loadindex[$i]]
				)
			);
		}
		$datanum++;
	}
}
fclose ($lfile);

// Delete column definitions
$res=$db->execute($sql_delvar,array($dataset_id,$varset));

echo " - ".$datanum." data rows imported<br/>";

return 0;
}
?>
