<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require $DBRoot."/lib/XMLDocument.php";

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
	s.dataset_id,
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
  and s.dataset_id in (".implode(',',$dataset_arr).")
order by s.dataset_id, t.track_id, p.trackpoint_no
";

function add_record($xmldoc, $xmlnum, $xmlrec, $xmltype, $dnum, $drec, $dpos) {
	// Reset xml array values except Dataset Id
	for ($i = 1; $i < $xmlnum; $i++)
		$xmlrec[$i]=NULL;
	// Fill the rest of the xml array with values from column record
	for ($i = 0; $i < $dnum; $i++) {
		if ($dpos[$i] != -1)
			$xmlrec[$dpos[$i]]=$drec[$i];
	}
	// Add a row to xml document
	$xmldoc->add_row();
	for ($i = 0; $i < $xmlnum; $i++) {
		$xmldoc->add_cell ($xmlrec[$i], $xmltype[$i]);
	}
}

$xmldoc = new XMLDocument("orn_trackpoint_pool.xml", "ORN Trackpoints");
$xmlnum = count($var_pos)+1;
$xmlrec = array();
$drec = array();

// Build header row in XML document
$xmldoc->add_row();
$xmldoc->add_cell ('Dataset Id', 'String');
foreach ($var_arr as $var)
	$xmldoc->add_cell ($var, 'String');

$colinfo=$db->query($sql_column,array());
$dnum=count($colinfo);
$xmltype=array_pad (array(), $xmlnum, NULL);
$xmltype[0]='Number';
$dpos=array_pad (array(), $dnum, -1);
for ($i = 0; $i < $dnum; $i++) {
	if (array_key_exists ($colinfo[$i]['header'], $var_pos)) {
		$dpos[$i]=$var_pos[$colinfo[$i]['header']]+1;
		if (in_array($colinfo[$i]['data_type'],array("integer","float")))
			$xmltype[$dpos[$i]]='Number';
		else
			$xmltype[$dpos[$i]]='String';
	}
}

// Retrieve data from database
$res=pg_query_params($DB,$sql_trackpoint,array())
	or die(pg_last_error()."\n");

while ($row=pg_fetch_assoc($res)) {
	$xmlrec[0]=$row['dataset_id'];
	for ($i = 0; $i < $dnum; $i++) {
		$drec[$i]=$row[$colinfo[$i]['column_name']];
	}
	add_record($xmldoc, $xmlnum, $xmlrec, $xmltype, $dnum, $drec, $dpos);
}
pg_free_result($res);
pg_close($DB);

// display and quit
$xmldoc->display();
?>
