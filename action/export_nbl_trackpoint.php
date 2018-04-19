<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require $DBRoot."/lib/XMLDocument.php";
$did=$dataset_id;

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
$sql_tz = "set time zone '".$tz."'";
$res = $db->execute($sql_tz);

// Log on to database using simple routine
require $DBRoot."/lib/DB.php";
$res = pg_query($DB,$sql_tz);

// SQL statement that gets information on all condition columns
$sql_cond="
select p.header, p.load_name
from p_column p, r_data r
where p.data_id = r.data_id
  and p.dataset_id = $1
  and r.table_name = 'p_nbl_condition'
order by p.order_no
";

// SQL statement that gets information on all track data columns
$sql_data="
select p.header, p.load_name
from p_column p, r_data r
where p.data_id = r.data_id
  and p.dataset_id = $1
  and r.table_name = 'd_nbl_track_data'
order by p.order_no
";

// SQL statement for selection of all column definitions
$sql_column="
select
	p.header,
	p.load_name,
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
	r.recording,
	s.setup,
	r.replicate,
	r.recording_time,
	f.file,
	f.file_name,
	t.track_id,
	t.track,
	t.taxon,
	t.animal_label,
	p.frame,
	p.time,
	p.x,
	p.y,
	p.z,
	sp.context_id COND_LIST DATA_LIST
from d_nbl_recording r, d_nbl_track t, d_nbl_trackpoint p, d_nbl_file f,
	p_nbl_setup s, p_nbl_setup_phase sp, p_nbl_context c COND_FROM DATA_FROM
where r.recording_id = t.recording_id
  and t.track_id = p.track_id
  and r.recording_id = f.recording_id
  and r.setup_id = s.setup_id
  and s.setup_id = sp.setup_id
  and sp.context_id = c.context_id
  and p.time between sp.start_time and sp.end_time
  and r.dataset_id = $1 COND_WHERE DATA_WHERE
order by r.recording, t.track, p.time
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

// Get condition columns information
$cond_arr=$db->query($sql_cond,array($did));
$cond_list=""; $cond_from=""; $cond_where="";
for ($i=0;$i<count($cond_arr);$i++) {
	$cond_list .= ",d".$i.".condition_value as ".$cond_arr[$i]['load_name'];
	$cond_from .= ",p_nbl_condition d".$i;
	$cond_where .= " and c.context_id = d".$i.".context_id";
	$cond_where .= " and d".$i.".condition_type = '".$cond_arr[$i]['header']."'";
}
$sql_trackpoint = str_replace ("COND_LIST", $cond_list, $sql_trackpoint);
$sql_trackpoint = str_replace ("COND_FROM", $cond_from, $sql_trackpoint);
$sql_trackpoint = str_replace ("COND_WHERE", $cond_where, $sql_trackpoint);

// Get track data columns information
$data_arr=$db->query($sql_data,array($did));
$data_list=""; $data_from=""; $data_where="";
for ($i=0;$i<count($data_arr);$i++) {
	$data_list .= ",e".$i.".data_value as ".$data_arr[$i]['load_name'];
	$data_from .= ",d_nbl_track_data e".$i;
	$data_where .= " and t.track_id = e".$i.".track_id";
	$data_where .= " and e".$i.".order_no = ".($i+1);
}
$sql_trackpoint = str_replace ("DATA_LIST", $data_list, $sql_trackpoint);
$sql_trackpoint = str_replace ("DATA_FROM", $data_from, $sql_trackpoint);
$sql_trackpoint = str_replace ("DATA_WHERE", $data_where, $sql_trackpoint);

$xmldoc = new XMLDocument("nbl_trackpoint_".$did.".xml", "NBL Trackpoints");
$xmlrec = array();
$colinfo = array(
	"column_name"=>array(),
	"data_type"=>array()
);

// Get column information and build header row in XML document
$xmldoc->add_row();
$result_column=pg_query_params($DB,$sql_column,array($did))
	or die(pg_last_error()."\n");
$colnum=0;
while ($colrow=pg_fetch_assoc($result_column)) {
	$colinfo['column_name'][$colnum]=$colrow['load_name'];
	$colinfo['data_type'][$colnum]=$colrow['data_type'];
	$xmlrec[$colnum]=NULL;
	$xmldoc->add_cell ($colrow['header'], 'String');
	$colnum++;
}

// Retrieve data from database
$result_point=pg_query_params($DB,$sql_trackpoint,array($did))
	or die(pg_last_error()."\n");

while ($prow=pg_fetch_assoc($result_point)) {
	for ($i = 0; $i < count($xmlrec); $i++) {
		$xmlrec[$i]=$prow[$colinfo['column_name'][$i]];
	}
	add_record($xmldoc, $xmlrec, $colinfo);
}
pg_free_result($result_point);
pg_close($DB);

// display and quit
$xmldoc->display();
?>
