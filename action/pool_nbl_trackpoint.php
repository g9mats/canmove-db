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

// SQL statement that gets all datasets in order
$sql_dataset="
select dataset_id
from p_dataset
where dataset_id in (".implode(',',$dataset_arr).")
order by dataset_id
";

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

function add_record($xmldoc, $xmlnum, $xmlrec, $xmltype, $dnum, $drec, $dpos) {
	// Reset xml array values except Dataset Id, Animal Id and Device Id
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

$xmldoc = new XMLDocument("nbl_trackpoint_pool.xml", "NBL Trackpoints");
$xmlnum = count($var_pos)+1;
$xmlrec = array();
$drec = array();

// Build header row in XML document
$xmldoc->add_row();
$xmldoc->add_cell ('Dataset Id', 'String');
foreach ($var_arr as $var)
	$xmldoc->add_cell ($var, 'String');

// Get all dataset_id values
$dres=$db->query($sql_dataset,array());
foreach ($dres as $drow) {
	$did=$drow['dataset_id'];

// Get condition columns information
$cond_arr=$db->query($sql_cond,array($did));
$cond_list=""; $cond_from=""; $cond_where="";
for ($i=0;$i<count($cond_arr);$i++) {
	$cond_list .= ",d".$i.".condition_value as ".$cond_arr[$i]['load_name'];
	$cond_from .= ",p_nbl_condition d".$i;
	$cond_where .= " and c.context_id = d".$i.".context_id";
	$cond_where .= " and d".$i.".condition_type = '".$cond_arr[$i]['header']."'";
}
$sql_tp = str_replace ("COND_LIST", $cond_list, $sql_trackpoint);
$sql_tp = str_replace ("COND_FROM", $cond_from, $sql_tp);
$sql_tp = str_replace ("COND_WHERE", $cond_where, $sql_tp);

// Get track data columns information
$data_arr=$db->query($sql_data,array($did));
$data_list=""; $data_from=""; $data_where="";
for ($i=0;$i<count($data_arr);$i++) {
	$data_list .= ",e".$i.".data_value as ".$data_arr[$i]['load_name'];
	$data_from .= ",d_nbl_track_data e".$i;
	$data_where .= " and t.track_id = e".$i.".track_id";
	$data_where .= " and e".$i.".order_no = ".($i+1);
}
$sql_tp = str_replace ("DATA_LIST", $data_list, $sql_tp);
$sql_tp = str_replace ("DATA_FROM", $data_from, $sql_tp);
$sql_tp = str_replace ("DATA_WHERE", $data_where, $sql_tp);

// Get column information
$colinfo=$db->query($sql_column,array($did));
$dnum=count($colinfo);
$xmltype=array_pad (array(), $xmlnum, NULL);
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
$xmlrec[0]=$did;
$xmltype[0]='Number';

// Retrieve data from database
$pres=pg_query_params($DB,$sql_tp,array($did))
	or die(pg_last_error()."\n");

while ($prow=pg_fetch_assoc($pres)) {
	for ($i = 0; $i < $dnum; $i++) {
		$drec[$i]=$prow[$colinfo[$i]['load_name']];
	}
	add_record($xmldoc, $xmlnum, $xmlrec, $xmltype, $dnum, $drec, $dpos);
}
pg_free_result($pres);

} // foreach $did
pg_close($DB);

// display and quit
$xmldoc->display();
?>
