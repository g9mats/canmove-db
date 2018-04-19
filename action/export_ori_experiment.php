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
	case r.column_name
		when 'condition_value' then 'text'
		else r.data_type
	end as data_type,
	r.mandatory
from p_column p, r_data r
where p.dataset_id = $1
  and p.data_id = r.data_id
  and p.data_subset = 'experiment'
order by p.order_no
";

// SQL statement for selection of all experiments and phases
$sql_experiment="
select
	a.animal_id,
	a.animal,
	c.capture_id,
	c.capture_time,
	e.setup_id,
	e.experiment_id,
	e.experiment_no,
	e.experiment_type,
	e.cage_top_diameter,
	e.cage_height,
	e.sensor_type,
	e.data_processing,
	e.data_format,
	e.latitude,
	e.longitude,
	e.location,
	e.operator_id as experiment_operator,
	e.measurement_time,
	e.remark as experiment_remark,
	p.phase_id,
	p.phase_no,
	p.start_time,
	p.end_time,
	p.middle_time,
	p.remark as phase_remark,
	s.setup
from d_ori_animal a, d_ori_capture c, d_ori_experiment e, d_ori_phase p,
	p_ori_setup s
where a.animal_id = c.animal_id
  and a.animal_id = e.animal_id
  and c.capture_id = e.capture_id
  and e.experiment_id = p.experiment_id
  and e.setup_id = s.setup_id
  and a.dataset_id = $1
order by a.animal, c.capture_time, e.experiment_no, p.phase_no
";

$sql_conddata="
select
	c.condition_value
from p_column p, r_data r,
	p_ori_setup s, p_ori_setup_phase sp, p_ori_condition c
where p.data_id = r.data_id
  and s.setup_id = sp.setup_id
  and sp.context_id = c.context_id
  and p.header = c.condition_type
  and p.dataset_id = $1
  and r.table_name = 'p_ori_condition'
  and s.setup_id = $2
order by order_no
";

$sql_experimentdata="
select
	data_value
from d_ori_experiment_data
where experiment_id = $1
order by order_no
";

$sql_phasedata="
select
	data_value
from d_ori_phase_data
where phase_id = $1
order by order_no
";

$xmldoc = new XMLDocument("ori_experiment_".$did.".xml", "ORI Experiments");
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
$experiment=$db->query($sql_experiment,array($did));

foreach ($experiment as $erow) {
	$aid=$erow['animal_id'];
	$cid=$erow['capture_id'];
	$eid=$erow['experiment_id'];
	$pid=$erow['phase_id'];
	$sid=$erow['setup_id'];
	$cdata=$db->query($sql_conddata,array($did,$sid)); $cdi=0;
	$edata=$db->query($sql_experimentdata,array($eid)); $edi=0;
	$pdata=$db->query($sql_phasedata,array($pid)); $pdi=0;
	for ($i = 0; $i < count($colinfo); $i++) {
		if ($colinfo[$i]['column_name']=="remark") {
			if ($colinfo[$i]['table_name']=="d_ori_experiment")
				$xmlrec[$i]=$erow['experiment_remark'];
			else
				$xmlrec[$i]=$erow['phase_remark'];
		} elseif ($colinfo[$i]['column_name']=="operator_id") {
			$xmlrec[$i]=$erow['experiment_operator'];
		} elseif ($colinfo[$i]['table_name']=="p_ori_condition") {
			$xmlrec[$i]=$cdata[$cdi++]['condition_value'];
		} elseif ($colinfo[$i]['table_name']=="d_ori_experiment_data") {
			$xmlrec[$i]=$edata[$edi++]['data_value'];
		} elseif ($colinfo[$i]['table_name']=="d_ori_phase_data") {
			$xmlrec[$i]=$pdata[$pdi++]['data_value'];
		} else {
			$xmlrec[$i]=$erow[$colinfo[$i]['column_name']];
		}
	}
	add_record($xmldoc, $xmlrec, $colinfo);
}

// display and quit
$xmldoc->display();
?>
