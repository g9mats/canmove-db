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
  and data_subset = 'windprofile'
order by order_no
";

// Build SQL select statements for windprofile records
$sql_windprofile="
select
	s.session_id,
	p.wind_profile_no,
	p.start_time,
	d.wind_data_no,
	d.z_height,
	d.x_speed,
	d.y_speed,
	d.wind_direction,
	d.wind_speed
from d_orn_session s, d_orn_wind_profile p, d_orn_wind_data d
where s.session_id = p.session_id
  and p.wind_profile_id = d.wind_profile_id
  and s.dataset_id = $1
order by s.session_id, p.wind_profile_no, d.wind_data_no
";

$xmldoc = new XMLDocument("orn_windprofile_".$did.".xml", "ORN Windprofiles");
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
$res=pg_query_params($DB,$sql_windprofile,array($did))
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
