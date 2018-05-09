<?php
/*
Creator: Mats J Svensson, CAnMove

This script loads ORI activity log data from staging area (l_ori_activity_log)
into destination table (d_ori_activity_log).
*/

function load_ori_activity_log ($dataset_id,$file_id) {

require "./canmove.inc";

require_once $DBRoot."/action/delete_import_ori_activity_log.php";

// Log on to database using common routine
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL statement that gets key values from file info
$sql_file="
select
	dataset_id,
	version,
	time_zone
from l_file
where file_id = $1
";

// SQL statement that delete records from d_ori_activity_log
$sql_del="
delete from d_ori_activity_log
where (phase_id, time, order_no) in (
	select p.phase_id, l.time, l.order_no
	from d_ori_animal a, d_ori_capture c, d_ori_experiment e, d_ori_phase p,
			d_ori_activity_log l
	where a.animal_id = c.animal_id
	  and c.capture_id = e.capture_id
	  and e.experiment_id = p.experiment_id
	  and p.phase_id = l.phase_id
	  and a.dataset_id = $1
	  and l.version = $2
	except
	select p.phase_id, cast (l.time as float8), l.order_no
	from d_ori_animal a, d_ori_capture c, d_ori_experiment e, d_ori_phase p,
			l_ori_activity_log l
	where a.animal_id = c.animal_id
	  and c.capture_id = e.capture_id
	  and e.experiment_id = p.experiment_id
	  and a.animal = l.animal
	  and c.capture_time = to_timestamp (l.capture_time, 'YYYY-MM-DD HH24:MI')
	  and e.experiment_no = l.experiment_no
	  and p.phase_no = l.phase_no
	  and a.dataset_id = $1
	  and l.version = $2
)
";

// SQL statement that updates records in d_ori_activity_log
$sql_upd="
update d_ori_activity_log x
set data_value = y.data_value
from d_ori_animal a, d_ori_capture c, d_ori_experiment e, d_ori_phase p,
		l_ori_activity_log y
where a.animal_id = c.animal_id
  and c.capture_id = e.capture_id
  and e.experiment_id = p.experiment_id
  and a.animal = y.animal
  and c.capture_time = to_timestamp (y.capture_time, 'YYYY-MM-DD HH24:MI')
  and e.experiment_no = y.experiment_no
  and p.phase_no = y.phase_no
  and p.phase_id = x.phase_id
  and y.version = x.version
  and cast (y.time as float8) = x.time
  and y.order_no = x.order_no
  and y.dataset_id = $1
  and y.version = $2
";

// SQL statement that inserts records into d_ori_activity_log
$sql_ins="
insert into d_ori_activity_log (
	phase_id, version,
	order_no, data_id, time,
	data_value
	)
select
	p.phase_id, l.version,
	l.order_no, l.data_id, cast (l.time as float8),
	l.data_value
from d_ori_animal a, d_ori_capture c, d_ori_experiment e, d_ori_phase p,
		l_ori_activity_log l
where a.animal_id = c.animal_id
  and c.capture_id = e.capture_id
  and e.experiment_id = p.experiment_id
  and a.animal = l.animal
  and c.capture_time = to_timestamp (l.capture_time, 'YYYY-MM-DD HH24:MI')
  and e.experiment_no = l.experiment_no
  and p.phase_no = l.phase_no
  and a.dataset_id = $1
  and l.version = $2
  and (l.animal, l.capture_time, l.experiment_no, l.phase_no,
			l.time, l.order_no) in (
	select animal, capture_time, experiment_no, phase_no, time, order_no
	from l_ori_activity_log
	where dataset_id = $1
	  and version = $2
	except
	select a.animal, to_char (c.capture_time, 'YYYY-MM-DD HH24:MI'),
			e.experiment_no, p.phase_no, cast (l.time as varchar), l.order_no
	from d_ori_animal a, d_ori_capture c, d_ori_experiment e, d_ori_phase p,
			d_ori_activity_log l
	where a.animal_id = c.animal_id
	  and c.capture_id = e.capture_id
	  and e.experiment_id = p.experiment_id
	  and p.phase_id = l.phase_id
	  and a.dataset_id = $1
	  and l.version = $2
)
";

// Get key values for file
$res = $db->query($sql_file, array($file_id));
$dataset_id = $res[0]['dataset_id'];
$version = $res[0]['version'];
$tz = $res[0]['time_zone'];
$sql_tz = "set time zone '".$tz."'";
$res = $db->execute($sql_tz);

// Synchronize a key combination
$res=$db->execute($sql_del, array($dataset_id,$version));
$res=$db->execute($sql_upd, array($dataset_id,$version));
$res=$db->execute($sql_ins, array($dataset_id,$version));

// Delete a key combination from staging area
delete_import_ori_activity_log($dataset_id,$version);

return 0;
}

?>
