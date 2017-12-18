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
  and data_subset = 'trackpoint'
order by order_no
";

// Build SQL select statements for trackpoint records
$sql_trackpoint="
select
	t.track_id,
	p.trackpoint_no,
	p.elapsed_time,
	p.x,
	p.y,
	p.z,
	p.air_density
from d_orn_session s, d_orn_track t, d_orn_trackpoint p
where s.session_id = t.session_id
  and t.track_id = p.track_id
  and s.dataset_id = $1
order by t.track_id, p.trackpoint_no
";

$xmldoc = new XMLDocument("orn_trackpoint_".$did.".xml", "ORN Trackpoints");
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
$res=pg_query_params($DB,$sql_trackpoint,array($did))
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
