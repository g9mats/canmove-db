<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require $DBRoot."/lib/XMLDocument.php";

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
from d_ori_animal a, d_ori_capture c
where a.animal_id = c.animal_id
  and a.dataset_id in (".implode(',',$dataset_arr).")
order by a.dataset_id, a.animal, c.capture_time
";

$sql_animaldata="
select
	data_value
from d_ori_animal_data
where animal_id = $1
order by order_no
";

$sql_capturedata="
select
	data_value
from d_ori_capture_data
where capture_id = $1
order by order_no
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

$xmldoc = new XMLDocument("ori_capture_pool.xml", "ORI Captures");
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
	for ($i = 0; $i < $dnum; $i++) {
		if ($colinfo[$i]['column_name']=="remark") {
			if ($colinfo[$i]['table_name']=="d_ori_animal")
				$drec[$i]=$crow['animal_remark'];
			else
				$drec[$i]=$crow['capture_remark'];
		} elseif ($colinfo[$i]['table_name']=="d_ori_animal_data") {
			$drec[$i]=$adata[$adi++]['data_value'];
		} elseif ($colinfo[$i]['table_name']=="d_ori_capture_data") {
			$drec[$i]=$cdata[$cdi++]['data_value'];
		} else {
			$drec[$i]=$crow[$colinfo[$i]['column_name']];
		}
	}
	add_record($xmldoc, $xmlnum, $xmlrec, $xmltype, $dnum, $drec, $dpos);
}

// display and quit
$xmldoc->display();
?>
