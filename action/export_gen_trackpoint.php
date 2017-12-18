<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require $DBRoot."/lib/XMLDocument.php";
$did=$dataset_id;
$ver=$version;
if ($ver=="")
	$ver=1;

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Log on to database using simple routine
require $DBRoot."/lib/DB.php";

// SQL statement for selection of all column definitions
$sql_column="
select
	p.header,
	r.column_name,
	r.data_type
from p_column p, r_data r
where p.data_id = r.data_id
  and p.dataset_id = $1
  and p.data_subset = 'trackpoint'
order by p.order_no
";

// Build SQL select statements for trackpoint records
$sql_trackpoint="
select
	a.animal,
	d.device,
	p.log_time,
	p.quality,
	p.latitude,
	p.longitude,
	p.speed,
	p.course,
	p.altitude
from d_gen_animal a, d_gen_track t, d_gen_device d, d_gen_trackpoint p
where a.animal_id = t.animal_id
  and t.track_id = d.track_id
  and d.device_id = p.device_id
  and a.dataset_id = $1
  and p.version = $2
order by a.animal, d.device, p.log_time
";

function add_record($xmldoc, $xmlrec, $colinfo) {
	$xmldoc->add_row();
	for ($i = 0; $i < count($xmlrec); $i++) {
		if (($colinfo['data_type'][$i]=="integer") || ($colinfo['data_type'][$i]=="float"))
			$xmldoc->add_cell ($xmlrec[$i], 'Number');
		else
			$xmldoc->add_cell ($xmlrec[$i], 'String');
	}
}

$xmldoc = new XMLDocument("gen_trackpoint_".$did.".xml", "GEN Trackpoints");
$xmlrec = array();
$colinfo = array(
	"column_name"=>array(),
	"data_type"=>array()
);

// Get column information and build header row in XML document
$xmldoc->add_row();
$colinfo['column_name'][0]="animal";
$colinfo['data_type'][0]="text";
$xmlrec[0]=NULL;
$xmldoc->add_cell ('Animal Id', 'String');
$colinfo['column_name'][1]="device";
$colinfo['data_type'][1]="text";
$xmlrec[1]=NULL;
$xmldoc->add_cell ("Device Id", 'String');
$result_column=pg_query_params($DB,$sql_column,array($did))
	or die(pg_last_error()."\n");
$colnum=2;
while ($colrow=pg_fetch_assoc($result_column)) {
	$colinfo['column_name'][$colnum]=$colrow['column_name'];
	$colinfo['data_type'][$colnum]=$colrow['data_type'];
	$xmlrec[$colnum]=NULL;
	/*
	echo $colnum.": ".$colrow['header'].", ".
		$colinfo['column_name'][$colnum].", ".
		$colinfo['data_type'][$colnum]."\n";
	*/
	$xmldoc->add_cell ($colrow['header'], 'String');
	$colnum++;
}
//echo "Number of columns: ".$colnum."\n";

// Retrieve data from database
$result_point=pg_query_params($DB,$sql_trackpoint,array($did,$ver))
	or die(pg_last_error()."\n");

while ($prow=pg_fetch_assoc($result_point)) {
	$xmlrec[0]=$prow['animal'];
	$xmlrec[1]=$prow['device'];
	for ($i = 2; $i < count($xmlrec); $i++) {
		$xmlrec[$i]=$prow[$colinfo['column_name'][$i]];
	}
	add_record($xmldoc, $xmlrec, $colinfo);
}
pg_free_result($result_point);
pg_close($DB);

// display and quit
$xmldoc->display();
?>
