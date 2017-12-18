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
where storage_type = 'TRR'
  and data_subset = 'track'
order by order_no
";

// Build SQL select statements for track records
$sql_track="
select
	t.track_id,
	t.start_time,
	t.site_id,
	t.radar_id,
	t.track_type,
	t.operator_id,
	t.quality_rating,
	t.overall_remark,
	t.edit_remark,
	b.target_type,
	b.target_remark,
	w.time_to_lock,
	w.create_time,
	w.ground_wind_dir,
	w.ground_wind_speed
from d_trr_track t
	left outer join d_trr_bird b on t.track_id = b.track_id
	left outer join d_trr_wind w on t.track_id = w.track_id
where t.dataset_id = $1
order by t.start_time
";

$xmldoc = new XMLDocument("trr_track_".$did.".xml", "TRR Tracks");
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
$res=pg_query_params($DB,$sql_track,array($did))
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
