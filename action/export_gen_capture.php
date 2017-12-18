<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require $DBRoot."/lib/XMLDocument.php";
$did=$dataset_id;

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL statement for selection of all column definitions
$sql_column="
select
	p.header,
	r.table_name,
	r.column_name,
	r.data_type,
	r.mandatory
from p_column p, r_data r
where p.dataset_id = $1
  and p.data_id = r.data_id
  and p.data_subset = 'capture'
order by p.order_no
";

// SQL statement for selection of all animals and captures
$sql_capture="
select
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
  and a.dataset_id = $1
order by a.animal, c.capture_time
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

$xmldoc = new XMLDocument("gen_capture_".$did.".xml", "GEN Captures");
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
$colinfo=$db->query($sql_column,array($did));
$xmldoc->add_row();
foreach ($colinfo as $col)
	$xmldoc->add_cell ($col['header'], 'String');

// Retrieve data from database
$capture=$db->query($sql_capture,array($did));

foreach ($capture as $crow) {
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
	for ($i = 0; $i < count($colinfo); $i++) {
		if ($colinfo[$i]['column_name']=="remark") {
			if ($colinfo[$i]['table_name']=="d_gen_animal")
				$xmlrec[$i]=$crow['animal_remark'];
			elseif ($colinfo[$i]['table_name']=="d_gen_capture")
				$xmlrec[$i]=$crow['capture_remark'];
			else
				if ($track_exists)
					$xmlrec[$i]=$track[0]['track_remark'];
				else
					$xmlrec[$i]=NULL;
		} elseif ($colinfo[$i]['table_name']=="d_gen_animal") {
			$xmlrec[$i]=$crow[$colinfo[$i]['column_name']];
		} elseif ($colinfo[$i]['table_name']=="d_gen_animal_data") {
			$xmlrec[$i]=$adata[$adi++]['data_value'];
		} elseif ($colinfo[$i]['table_name']=="d_gen_capture") {
			$xmlrec[$i]=$crow[$colinfo[$i]['column_name']];
		} elseif ($colinfo[$i]['table_name']=="d_gen_capture_data") {
			$xmlrec[$i]=$cdata[$cdi++]['data_value'];
		} elseif ($colinfo[$i]['table_name']=="d_gen_track") {
			if ($track_exists)
				$xmlrec[$i]=$track[0][$colinfo[$i]['column_name']];
			else
				$xmlrec[$i]=NULL;
		} elseif ($colinfo[$i]['table_name']=="d_gen_track_data") {
			if ($track_exists) {
				$xmlrec[$i]=$tdata[$tdi++]['data_value'];
			} else
				$xmlrec[$i]=NULL;
		} elseif ($colinfo[$i]['table_name']=="d_gen_device") {
			$xmlrec[$i]=NULL;
			if ($track_exists) {
				if ($colinfo[$i]['column_name']=="device") {
					$devno++;
					$device=$db->query($sql_device,array($tid,$devno));
					if (count($device)>0) {
						$xmlrec[$i]=$device[0]['device'];
						$device_exists=true;
					} else
						$device_exists=false;
				} else {
					if ($device_exists)
						$xmlrec[$i]=$device[0][$colinfo[$i]['column_name']];
				}
			}
		}
	}
	add_record($xmldoc, $xmlrec, $colinfo);

	if ($track_exists && count($track)>1) {
		$tid=$track[1]['track_id'];
		$tdata=$db->query($sql_trackdata,array($tid)); $tdi=0;
		$devno=0;
		for ($i = 0; $i < count($colinfo); $i++) {
			if ($colinfo[$i]['table_name']=="d_gen_track") {
				$xmlrec[$i]=$track[1][$colinfo[$i]['column_name']];
			} elseif ($colinfo[$i]['table_name']=="d_gen_track_data") {
				$xmlrec[$i]=$tdata[$tdi++]['data_value'];
			} elseif ($colinfo[$i]['table_name']=="d_gen_device") {
				$xmlrec[$i]=NULL;
				if ($colinfo[$i]['column_name']=="device") {
					$devno++;
					$device=$db->query($sql_device,array($tid,$devno));
					if (count($device)>0) {
						$xmlrec[$i]=$device[0]['device'];
						$device_exists=true;
					} else
						$device_exists=false;
				} else {
					if ($device_exists)
						$xmlrec[$i]=$device[0][$colinfo[$i]['column_name']];
				}
			}
		}
		add_record($xmldoc, $xmlrec, $colinfo);
	}
}

// display and quit
$xmldoc->display();
?>
