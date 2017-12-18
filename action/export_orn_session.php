<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require $DBRoot."/lib/XMLDocument.php";
$did=$dataset_id;

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Log on to database using simple routine
require $DBRoot."/lib/DB.php";

// SQL statement for selection of all column definitions
$sql_column="
select
	header,
	column_name,
	data_type
from r_data
where storage_type = 'ORN'
  and data_subset = 'session'
order by order_no
";

// Build SQL select statements for session records
$sql_session="
select
	session_id,
	location,
	session_time,
	latitude,
	longitude,
	altitude,
	declination,
	height_datum,
	height_source,
	wind_source,
	anemometer_height,
	taxa_file,
	activity_file,
	remark
from d_orn_session
where dataset_id = $1
order by session_time
";

$xmldoc = new XMLDocument("orn_session_".$did.".xml", "ORN Sessions");
$xmlrec = array();

function add_record($xmldoc, $xmlrec, $colinfo) {
	$xmldoc->add_row();
	for ($i = 0; $i < count($xmlrec); $i++) {
		if (($colinfo[$i]['data_type']=="integer") || ($colinfo[$i]['data_type']=="float"))
			$xmldoc->add_cell ($xmlrec[$i], 'Number');
		else
			$xmldoc->add_cell ($xmlrec[$i], 'String');
	}
}

// Get column information and build header row in XML document
$colinfo=$db->query($sql_column,array());
$xmldoc->add_row();
foreach ($colinfo as $col)
	$xmldoc->add_cell ($col['header'], 'String');

// Retrieve data from database
$res=pg_query_params($DB,$sql_session,array($did))
	or die(pg_last_error()."\n");

while ($row=pg_fetch_assoc($res)) {
	for ($i = 0; $i < count($colinfo); $i++) {
		$xmlrec[$i]=$row[$colinfo[$i]['column_name']];
	}
	add_record($xmldoc, $xmlrec, $colinfo);
}
pg_free_result($res);
pg_close($DB);

// display and quit
$xmldoc->display();
?>
