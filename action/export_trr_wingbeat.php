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
  and data_subset = 'wingbeat'
order by order_no
";

// Build SQL select statements for wingbeat records
$sql_wingbeat="
select
	t.track_id,
	w.start_time,
	w.duration,
	w.sense,
	w.file_name,
	w.wbfpeakfq,
	w.bffpeakfq,
	w.wbfpeakpower,
	w.bffpeakpower,
	w.wbf_lo_limit,
	w.wbf_hi_limit,
	w.bff_lo_limit,
	w.bff_hi_limit,
	w.coeff_a_zero,
	w.fsamp,
	w.fftsize
from d_trr_track t, d_trr_wingbeat w
where t.track_id = w.track_id
  and t.dataset_id = $1
order by t.track_id, w.start_time
";

$xmldoc = new XMLDocument("trr_wingbeat_".$did.".xml", "TRR Wingbeats");
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
$res=pg_query_params($DB,$sql_wingbeat,array($did))
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
