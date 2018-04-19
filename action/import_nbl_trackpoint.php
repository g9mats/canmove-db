<?php
/*
Creator: Mats J Svensson, CAnMove

This script loads NBL tracking data from tab separated text file into staging
area.

The first row should contain agreed headers defined in table r_data but
do not need to appear in a ceratin order. The column information is loaded
into l_column. If an unknown header is found the column will be ignored.

The actual Trackpoint data is loaded into table l_nbl_trackpoint for
subsequent validation.
*/

function import_nbl_trackpoint ($fname, $separator, $file_id) {
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
	time_zone
from l_file
where file_id = $1
";

// SQL statement that deletes any previously loaded header definitions
$sql_delvar="
delete from l_column
where dataset_id = $1
  and data_subset = 'trackpoint'
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
where storage_type = 'NBL'
  and data_subset = 'trackpoint'
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
  and d.storage_type = 'NBL'
  and d.data_subset = 'trackpoint'
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

// SQL statement that deletes any previously loaded data
$sql_deldata="
delete from l_nbl_trackpoint
where dataset_id = $1
";

// SQL statement that checks for previously loaded column definitions in project table
$sql_colnum="
select count(*) colnum
from p_column
where dataset_id = $1
  and data_subset = 'trackpoint'
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
  and data_subset = 'trackpoint'
order by order_no
";

// SQL statement that checks for differences from previously loaded column definitions in project table
$sql_coldiff="
select header, 'missing' status
from p_column
where dataset_id = $1
  and data_subset = 'trackpoint'
except
select header, 'missing' status
from l_column
where dataset_id = $1
  and data_subset = 'trackpoint'
union
select header, 'unknown' status
from l_column
where dataset_id = $1
  and data_subset = 'trackpoint'
except
select header, 'unknown' status
from p_column
where dataset_id = $1
  and data_subset = 'trackpoint'
order by header
";

// Get key values for file
$res = $db->query($sql_file, array($file_id));
$dataset_id = $res[0]['dataset_id'];
$tz = $res[0]['time_zone'];
$sql_tz = "set time zone '".$tz."'";
$res = $db->execute($sql_tz);

// Start build of SQL statment for insert of data row
$sql_insdata=
	"insert into l_nbl_trackpoint (dataset_id";
$sql_insdata2=$dataset_id;

// Delete any old column definitions
$res=$db->execute($sql_delvar,array($dataset_id));
//echo pg_affected_rows($result)." column definitions deleted<br/>";

// Delete any previously loaded data from l_nbl_trackpoint
$res=$db->execute($sql_deldata,array($dataset_id));
//echo pg_affected_rows($result)." data rows deleted<br/>";

$colnum=0;		// Count columns in text file
$loadcolnum=0;	// Count columns to load in text file
$varnum=0;		// Count optional columns for loading into columns c1,...
// Keep track of data type and any case restrictions in an array
$colinfo=array("data_type"=>array(),"case_type"=>array());
// Keep track of column references for loading in an array
// They are less than or equal to total number of columns
$loadindex=array();
// Walk through all headers
foreach ($headers as $htext) {
	// Fetch a column definition for current header
	if (!$res=$db->query($sql_selvar,array($htext))) {
		// Exit if header definition is not found
		echo "Invalid header: '".$htext."'<br/>";
		return 1;
	}
	$row=$res[0];
	// Save data type and any case restriction
	$colinfo['data_type'][$colnum]=$row['data_type'];
	$colinfo['case_type'][$colnum]=$row['case_type'];
	$loadindex[$loadcolnum++]=$colnum++;
	// Define destination column for data in table l_nbl_trackpoint
	if ($row['column_type']=="var") {
		$varnum++;
		$lname=$row['load_name'].$varnum;
	} else
		$lname=$row['load_name'];
	// Insert one column definition in table l_column
	$res=$db->execute($sql_insvar,
		array(
			$dataset_id,
			'trackpoint',
			$loadcolnum,
			$row['data_id'],
			$lname,
			$row['header']
		)
	);
	// Continue to build data insert statement: column name and variable ref.
	$sql_insdata.=",".$lname;
	$sql_insdata2.=",$".$loadcolnum;
}
// Make final construction of data insert statement
$sql_insdata.=") values (".$sql_insdata2.")";
echo " - ".$loadcolnum." column definitions imported<br/>";

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
$loaddata=array();	// Create array for only those columns that should be loaded
while (($buffer = fgets ($lfile)) !== FALSE) { // Read rest of file
	$buffer = rtrim ($buffer, "\t\r\n");
	$data = array_pad (explode ($schar, $buffer), $colnum, "");
	$notnull=0;
	for ($i = 0; $i < $loadcolnum; $i++) {	// Walk through all columns
		$li=$loadindex[$i];
		$data[$li]=trim($data[$li]);	// Trim spaces
		if ($data[$li]=="")			// Convert empty string to NULL
			$data[$li]=NULL;
		else {
			$notnull++;
			// Switch "," to "." for float
			if ($colinfo['data_type'][$li]=="float")
				$data[$li]=str_replace(",", ".", $data[$li]);
			// Execute case restriction
			if ($colinfo['case_type'][$li]=="upper")
				$data[$li]=strtoupper($data[$li]);
			if ($colinfo['case_type'][$li]=="lower")
				$data[$li]=strtolower($data[$li]);
		}
		// Save column data into the new array
		$loaddata[$i]=$data[$li];
	}
	if ($notnull > 0) {
		// Insert one data row into table l_nbl_trackpoint
		$res=$db->execute($sql_insdata,$loaddata);
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
