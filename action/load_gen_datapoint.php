<?php
/*
Creator: Mats J Svensson, CAnMove

This script loads GEN datapoint data from staging area (l_gen_datapoint) into
destination table (d_gen_datapoint).
*/

function load_gen_datapoint ($dataset_id,$file_id) {

require "./canmove.inc";

require_once $DBRoot."/action/delete_import_gen_datapoint.php";

// Log on to database using common routine
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL statement that gets key values from file info
$sql_file="
select
	device_id,
	period,
	version,
	varset
from l_file
where file_id = $1
";

// SQL statement that delete records from d_gen_datapoint
$sql_del="
delete from d_gen_datapoint
where device_id = $2
  and period = $3
  and version = $4
  and varset = $5
  and (order_no, log_time) in (
	select order_no, log_time
	from d_gen_datapoint
	where device_id = $2
	  and period = $3
	  and version = $4
	  and varset = $5
	except
	select order_no, to_timestamp (log_time, 'YYYY-MM-DD HH24:MI')
	from l_gen_datapoint
	where dataset_id = $1
	  and device_id = $2
	  and period = $3
	  and version = $4
	  and varset = $5
)
";

// SQL statement that updates records in d_gen_datapoint
$sql_upd="
update d_gen_datapoint x
set data_value = y.data_value
from l_gen_datapoint y
where y.device_id = x.device_id
  and y.period = x.period
  and y.version = x.version
  and y.varset = x.varset
  and y.order_no = x.order_no
  and to_timestamp (y.log_time, 'YYYY-MM-DD HH24:MI') = x.log_time
  and y.dataset_id = $1
  and y.device_id = $2
  and y.period = $3
  and y.version = $4
  and y.varset = $5
";

// SQL statement that inserts records into d_gen_datapoint
$sql_ins="
insert into d_gen_datapoint (
	device_id, period, version, varset,
	order_no, data_id, log_time,
	data_value
	)
select
	device_id, period, version, varset,
	order_no, data_id, to_timestamp (log_time, 'YYYY-MM-DD HH24:MI'),
	data_value
from l_gen_datapoint
where dataset_id = $1
  and device_id = $2
  and period = $3
  and version = $4
  and varset = $5
  and (order_no, to_timestamp (log_time, 'YYYY-MM-DD HH24:MI')) in (
	select order_no, to_timestamp (log_time, 'YYYY-MM-DD HH24:MI')
	from l_gen_datapoint
	where dataset_id = $1
	  and device_id = $2
	  and period = $3
	  and version = $4
	  and varset = $5
	except
	select order_no, log_time
	from d_gen_datapoint
	where device_id = $2
	  and period = $3
	  and version = $4
	  and varset = $5
)
";

// Get key values for file
$res = $db->query($sql_file, array($file_id));
$device_id = $res[0]['device_id'];
$period = $res[0]['period'];
$version = $res[0]['version'];
$varset = $res[0]['varset'];

// Synchronize a key combination
$res=$db->execute($sql_del,
			array($dataset_id,$device_id,$period,$version,$varset));
$res=$db->execute($sql_upd,
			array($dataset_id,$device_id,$period,$version,$varset));
$res=$db->execute($sql_ins,
			array($dataset_id,$device_id,$period,$version,$varset));

// Delete a key combination from staging area
delete_import_gen_datapoint($dataset_id,$device_id,$period,$version,$varset);

return 0;
}

?>
