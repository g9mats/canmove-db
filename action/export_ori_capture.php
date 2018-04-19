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
from d_ori_animal a, d_ori_capture c
where a.animal_id = c.animal_id
  and a.dataset_id = $1
order by a.animal, c.capture_time
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

$xmldoc = new XMLDocument("ori_capture_".$did.".xml", "ORI Captures");
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
	for ($i = 0; $i < count($colinfo); $i++) {
		if ($colinfo[$i]['column_name']=="remark") {
			if ($colinfo[$i]['table_name']=="d_ori_animal")
				$xmlrec[$i]=$crow['animal_remark'];
			else
				$xmlrec[$i]=$crow['capture_remark'];
		} elseif ($colinfo[$i]['table_name']=="d_ori_animal_data") {
			$xmlrec[$i]=$adata[$adi++]['data_value'];
		} elseif ($colinfo[$i]['table_name']=="d_ori_capture_data") {
			$xmlrec[$i]=$cdata[$cdi++]['data_value'];
		} else {
			$xmlrec[$i]=$crow[$colinfo[$i]['column_name']];
		}
	}
	add_record($xmldoc, $xmlrec, $colinfo);
}

// display and quit
$xmldoc->display();
?>
