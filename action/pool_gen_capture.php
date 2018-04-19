<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require $DBRoot."/lib/XMLDocument.php";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
$sql_tz = "set time zone '".$tz."'";
$res = $db->execute($sql_tz);

// SQL statement for selection of all column definitions
$sql_column="
select
	p.header,
	r.table_name,
	r.column_name,
	r.data_type
from p_column p, r_data r
where p.dataset_id = $1
  and p.data_id = r.data_id
  and p.data_subset = 'capture'
order by p.order_no
";

// SQL statement for selection of all animals and captures
$sql_capture="
select
	a.dataset_id,
	a.animal_id,
	a.animal,
	a.taxon,
	a.remark as animal_remark,
	c.capture_id,
	c.capture_time,
	c.latitude,
	c.longitude,
	c.location,
	c.operator_id,
	c.remark as capture_remark
from d_gen_animal a, d_gen_capture c
where a.animal_id = c.animal_id
  and a.dataset_id in (".implode(',',$dataset_arr).")
order by a.dataset_id, a.animal, c.capture_time
";

$sql_animaldata="
select
	data_value
from d_gen_animal_data
where animal_id = $1
order by order_no
";

$sql_capturedata="
select
	data_value
from d_gen_capture_data
where capture_id = $1
order by order_no
";

$sql_track="
select
	track_id,
	start_capture_id,
	end_capture_id,
	'END' as track_event,
	remark as track_remark
from d_gen_track
where end_capture_id = $1
union
select
	track_id,
	start_capture_id,
	end_capture_id,
	'START' as track_event,
	remark as track_remark
from d_gen_track
where start_capture_id = $1
order by track_event
";

$sql_trackdata="
select
	data_value
from d_gen_track_data
where track_id = $1
order by order_no
";

$sql_device="
select
	device,
	device_model_id
from d_gen_device
where track_id = $1
  and order_no = $2
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

$xmldoc = new XMLDocument("gen_capture_pool.xml", "GEN Captures");
$xmlnum = count($var_pos)+1;
$xmlrec = array();
$drec = array();

// Build header row in XML document
$xmldoc->add_row();
$xmldoc->add_cell ('Dataset Id', 'String');
foreach ($var_arr as $var)
	$xmldoc->add_cell ($var, 'String');

// Retrieve data from database
$capture=$db->query($sql_capture,array());
$old_did=-1;

foreach ($capture as $crow) {
	$did=$crow['dataset_id'];
	if ($did != $old_did) {
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
		$old_did=$did;
	}
	$aid=$crow['animal_id'];
	$cid=$crow['capture_id'];
	$adata=$db->query($sql_animaldata,array($aid)); $adi=0;
	$cdata=$db->query($sql_capturedata,array($cid)); $cdi=0;
	$track=$db->query($sql_track,array($cid));
	if (count($track)>0) {
		$tid=$track[0]['track_id'];
		$track_exists=true;
		$tdata=$db->query($sql_trackdata,array($tid)); $tdi=0;
	} else
		$track_exists=false;
	$devno=0;
	for ($i = 0; $i < $dnum; $i++) {
		if ($colinfo[$i]['column_name']=="remark") {
			if ($colinfo[$i]['table_name']=="d_gen_animal")
				$drec[$i]=$crow['animal_remark'];
			elseif ($colinfo[$i]['table_name']=="d_gen_capture")
				$drec[$i]=$crow['capture_remark'];
			else
				if ($track_exists)
					$drec[$i]=$track[0]['track_remark'];
				else
					$drec[$i]=NULL;
		} elseif ($colinfo[$i]['table_name']=="d_gen_animal") {
			$drec[$i]=$crow[$colinfo[$i]['column_name']];
		} elseif ($colinfo[$i]['table_name']=="d_gen_animal_data") {
			$drec[$i]=$adata[$adi++]['data_value'];
		} elseif ($colinfo[$i]['table_name']=="d_gen_capture") {
			$drec[$i]=$crow[$colinfo[$i]['column_name']];
		} elseif ($colinfo[$i]['table_name']=="d_gen_capture_data") {
			$drec[$i]=$cdata[$cdi++]['data_value'];
		} elseif ($colinfo[$i]['table_name']=="d_gen_track") {
			if ($track_exists)
				$drec[$i]=$track[0][$colinfo[$i]['column_name']];
			else
				$drec[$i]=NULL;
		} elseif ($colinfo[$i]['table_name']=="d_gen_track_data") {
			if ($track_exists) {
				$drec[$i]=$tdata[$tdi++]['data_value'];
			} else
				$drec[$i]=NULL;
		} elseif ($colinfo[$i]['table_name']=="d_gen_device") {
			$drec[$i]=NULL;
			if ($track_exists) {
				if ($colinfo[$i]['column_name']=="device") {
					$devno++;
					$device=$db->query($sql_device,array($tid,$devno));
					if (count($device)>0) {
						$drec[$i]=$device[0]['device'];
						$device_exists=true;
					} else
						$device_exists=false;
				} else {
					if ($device_exists)
						$drec[$i]=$device[0][$colinfo[$i]['column_name']];
				}
			}
		}
	}
	add_record($xmldoc, $xmlnum, $xmlrec, $xmltype, $dnum, $drec, $dpos);

	if ($track_exists && count($track)>1) {
		$tid=$track[1]['track_id'];
		$tdata=$db->query($sql_trackdata,array($tid)); $tdi=0;
		$devno=0;
		for ($i = 0; $i < $dnum; $i++) {
			if ($colinfo[$i]['table_name']=="d_gen_track") {
				$drec[$i]=$track[1][$colinfo[$i]['column_name']];
			} elseif ($colinfo[$i]['table_name']=="d_gen_track_data") {
				$drec[$i]=$tdata[$tdi++]['data_value'];
			} elseif ($colinfo[$i]['table_name']=="d_gen_device") {
				$drec[$i]=NULL;
				if ($colinfo[$i]['column_name']=="device") {
					$devno++;
					$device=$db->query($sql_device,array($tid,$devno));
					if (count($device)>0) {
						$drec[$i]=$device[0]['device'];
						$device_exists=true;
					} else
						$device_exists=false;
				} else {
					if ($device_exists)
						$drec[$i]=$device[0][$colinfo[$i]['column_name']];
				}
			}
		}
		add_record($xmldoc, $xmlnum, $xmlrec, $xmltype, $dnum, $drec, $dpos);
	}
}

// display and quit
$xmldoc->display();
?>
