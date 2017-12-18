<?php
/*
Creator: Mats J Svensson, CAnMove

This script loads NBL trackpoint data from staging area (l_nbl_trackpoint) into
destination tables (p_nbl_context, p_nbl_condition, p_nbl_setup, p_nbl_setup_phase, d_nbl_recording, d_nbl_track, d_nbl_track_data, d_nbl_file, d_nbl_trackpoint).
*/

function load_nbl_trackpoint ($dataset_id,$file_id) {

require "./canmove.inc";

require_once $DBRoot."/action/delete_import_nbl_trackpoint.php";

// Log on to database using common routine
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

require_once $DBRoot."/action/delete_data_nbl_trackpoint.php";
require_once $DBRoot."/action/delete_import_nbl_trackpoint.php";

// SQL statement that gets information on all condition columns
$sql_cond="
select p.header, p.load_name
from p_column p, r_data r
where p.data_id = r.data_id
  and p.dataset_id = $1
  and r.table_name = 'p_nbl_condition'
order by p.order_no
";

// SQL statement that gets information on all contexts
$sql_cont="
select distinct COND_LIST
from l_nbl_trackpoint
where dataset_id = $1
order by COND_LIST
";

// SQL statement that inserts a context record
$sql_cont_ins="
insert into p_nbl_context (
	dataset_id, context
) values ($1, $2)
";

// SQL statement that inserts a condition record
$sql_cond_ins="
insert into p_nbl_condition (
	context_id, condition_type, condition_value
) select distinct context_id, $3, $4
from p_nbl_context
where dataset_id = $1
  and context = $2
";

// SQL statement that inserts setups
$sql_setup_ins="
insert into p_nbl_setup (
	dataset_id, setup
)
select distinct cast ($1 as integer), setup
from l_nbl_trackpoint
where dataset_id = $1
";

// SQL statement that inserts setup phases
$sql_phase_ins="
insert into p_nbl_setup_phase (
	setup_id, start_time, end_time, context_id
)
select
	s.setup_id,
	min(cast(p.time as float)),
	max(cast(p.time as float)),
	c.context_id
from p_nbl_setup s, l_nbl_trackpoint p, p_nbl_context c COND_FROM
where s.dataset_id = p.dataset_id
  and s.dataset_id = c.dataset_id
  and s.setup = p.setup
  and s.dataset_id = $1
  COND_WHERE
group by s.setup_id,c.context_id COND_LIST
order by s.setup_id,c.context_id COND_LIST
";

// SQL statement that inserts recordings
$sql_rec="
insert into d_nbl_recording (
	dataset_id, recording, setup_id, replicate, recording_time
)
select distinct
	p.dataset_id,
	cast(p.recording as integer) as recording,
	s.setup_id,
	cast(p.replicate as integer) as replicate,
	cast(p.recording_time as timestamp) as recording_time
from p_nbl_setup s, l_nbl_trackpoint p
where s.dataset_id = p.dataset_id
  and s.setup = p.setup
  and s.dataset_id = $1
order by recording
";

// SQL statement that inserts tracks
$sql_track="
insert into d_nbl_track (
	recording_id, track, itis_tsn, taxon, animal_label
)
select distinct
	r.recording_id,
	cast(p.track as integer) as track,
	t.tsn,
	p.taxon,
	p.animal_label
from d_nbl_recording r, l_nbl_trackpoint p, r_taxon t
where r.recording = cast(p.recording as integer)
  and r.dataset_id = p.dataset_id
  and p.taxon = t.complete_name
  and r.dataset_id = $1
  and t.name_usage = 'valid'
order by track
";

// SQL statement that gets information on all track data columns
$sql_track_data="
select r.data_id, p.load_name
from p_column p, r_data r
where p.data_id = r.data_id
  and p.dataset_id = $1
  and r.table_name = 'd_nbl_track_data'
order by p.order_no
";

// SQL statement that inserts track data
$sql_track_data_ins="
insert into d_nbl_track_data (
	track_id, order_no, data_id, data_value
)
select distinct
	t.track_id,
	cast($2 as integer),
	cast($3 as integer),
	LOAD_NAME
from d_nbl_recording r, d_nbl_track t, l_nbl_trackpoint p
where r.recording_id = t.recording_id
  and r.dataset_id = p.dataset_id
  and r.recording = cast(p.recording as integer)
  and t.track = cast(p.track as integer)
  and r.dataset_id = $1
order by t.track_id
";

// SQL statement that inserts files
$sql_file="
insert into d_nbl_file (
	recording_id, file, file_name
)
select distinct
	r.recording_id,
	cast(p.file as integer) as file,
	p.file_name
from d_nbl_recording r, l_nbl_trackpoint p
where r.dataset_id = p.dataset_id
  and r.recording = cast(p.recording as integer)
  and r.dataset_id = $1
order by file
";

// SQL statement that inserts trackpoints
$sql_trackpoint="
insert into d_nbl_trackpoint (
	track_id, file_id, frame, time, x, y, z
)
select distinct
	t.track_id,
	f.file_id,
	cast(p.frame as integer),
	cast(p.time as float),
	cast(p.x as float),
	cast(p.y as float),
	cast(p.z as float)
from d_nbl_recording r, d_nbl_track t, d_nbl_file f, l_nbl_trackpoint p
where r.recording_id = t.recording_id
  and r.dataset_id = p.dataset_id
  and r.recording = cast(p.recording as integer)
  and t.track = cast(p.track as integer)
  and r.recording_id = f.recording_id
  and r.dataset_id = $1
order by t.track_id
";

// Delete previously loaded data from database
delete_data_nbl_trackpoint($dataset_id);

// Get condition columns information
$cond_arr=$db->query($sql_cond,array($dataset_id));
$cond_list="";
for ($i=0;$i<count($cond_arr);$i++) {
	$cond_list .= ",".$cond_arr[$i]['load_name'];
}

// Get Context information
$sql_cont = str_replace ("COND_LIST", ltrim($cond_list,","), $sql_cont);
$cont_arr=$db->query($sql_cont,array($dataset_id));

// Insert contexts and conditions
for ($i=0;$i<count($cont_arr);$i++) {
	$res=$db->execute($sql_cont_ins, array($dataset_id,$i+1));
	for ($j=0;$j<count($cond_arr);$j++) {
		$res=$db->execute($sql_cond_ins,
			array(
				$dataset_id,
				$i+1,
				$cond_arr[$j]['header'],
				$cont_arr[$i][$cond_arr[$j]['load_name']]
			));
	}
}

// Insert setups
$res=$db->execute($sql_setup_ins, array($dataset_id));

// Insert setup phases
$cond_from=""; $cond_where="";
for ($i=0;$i<count($cond_arr);$i++) {
	$cond_from .= ",p_nbl_condition d".$i;
	$cond_where .= " and c.context_id=d".$i.".context_id";
	$cond_where .= " and p.".$cond_arr[$i]['load_name']."=d".$i.".condition_value";
	$cond_where .= " and d".$i.".condition_type='".$cond_arr[$i]['header']."'";
}
$sql_phase_ins = str_replace ("COND_LIST", $cond_list, $sql_phase_ins);
$sql_phase_ins = str_replace ("COND_FROM", $cond_from, $sql_phase_ins);
$sql_phase_ins = str_replace ("COND_WHERE", $cond_where, $sql_phase_ins);
$res=$db->execute($sql_phase_ins, array($dataset_id));

// Insert recordings
$res=$db->execute($sql_rec, array($dataset_id));

// Insert tracks
$res=$db->execute($sql_track, array($dataset_id));

// Insert track data
$track_data=$db->query($sql_track_data, array($dataset_id));
for ($i=0;$i<count($track_data);$i++) {
	$sql_temp = str_replace ("LOAD_NAME", $track_data[$i]['load_name'], $sql_track_data_ins);
	$res=$db->execute($sql_temp,
			array(
				$dataset_id,
				$i+1,
				$track_data[$i]['data_id']
				));
}

// Insert files
$res=$db->execute($sql_file, array($dataset_id));

// Insert trackpoints
$res=$db->execute($sql_trackpoint, array($dataset_id));

// Delete a key combination from staging area
delete_import_nbl_trackpoint($dataset_id);

return 0;
}
?>
