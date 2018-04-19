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
  and p.data_subset = 'estimation'
order by p.order_no
";

// SQL statement for selection of all estimations
$sql_estimation="
select
	a.animal_id,
	a.animal,
	c.capture_id,
	c.capture_time,
	e.experiment_no,
	p.phase_no,
	d.estimation_id,
	d.operator_id,
	d.activity,
	d.concentration,
	d.direction,
	d.modality,
	d.act_plus_conc,
	d.remark
from d_ori_animal a, d_ori_capture c, d_ori_experiment e, d_ori_phase p,
	d_ori_estimation d
where a.animal_id = c.animal_id
  and a.animal_id = e.animal_id
  and c.capture_id = e.capture_id
  and e.experiment_id = p.experiment_id
  and p.phase_id = d.phase_id
  and a.dataset_id = $1
order by a.animal, c.capture_time, e.experiment_no, p.phase_no
";

$xmldoc = new XMLDocument("ori_estimation_".$did.".xml", "ORI Estimations");
$xmlrec = array();

function add_record($xmldoc, $xmlrec, $colinfo) {
	$xmldoc->add_row();
	for ($i = 0; $i < count($xmlrec); $i++) {
		$cell_data=$xmlrec[$i];
		if (($colinfo[$i]['data_type']=="integer") || ($colinfo[$i]['data_type']=="float"))
			$xmldoc->add_cell ($cell_data, 'Number');
		else
			$xmldoc->add_cell ($cell_data, 'String');
	}
}

// Get column information and build header row in XML document
$colinfo=$db->query($sql_column,array($did));
$xmldoc->add_row();
foreach ($colinfo as $col)
	$xmldoc->add_cell ($col['header'], 'String');

// Retrieve data from database
$estimation=$db->query($sql_estimation,array($did));

foreach ($estimation as $erow) {
	$eid=$erow['estimation_id'];
	for ($i = 0; $i < count($colinfo); $i++) {
		$xmlrec[$i]=$erow[$colinfo[$i]['column_name']];
	}
	add_record($xmldoc, $xmlrec, $colinfo);
}

// display and quit
$xmldoc->display();
?>
