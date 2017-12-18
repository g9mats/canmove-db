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
	case r.column_name
		when 'condition_value' then 'text'
		else r.data_type
	end as data_type
from p_column p, r_data r
where p.dataset_id = $1
  and p.data_id = r.data_id
  and p.data_subset = 'experiment'
order by p.order_no
";

// SQL statement for selection of all experiments and phases
$sql_experiment="
select
	a.dataset_id,
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
  and a.dataset_id in (".implode(',',$dataset_arr).")
order by a.dataset_id, a.animal, c.capture_time, e.experiment_no, p.phase_no
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

$xmldoc = new XMLDocument("ori_experiment_pool.xml", "ORI Experiments");
$xmlnum = count($var_pos)+1;
$xmlrec = array();
$drec = array();

// Build header row in XML document
$xmldoc->add_row();
$xmldoc->add_cell ('Dataset Id', 'String');
foreach ($var_arr as $var)
	$xmldoc->add_cell ($var, 'String');

// Retrieve data from database
$experiment=$db->query($sql_experiment,array());
$old_did=-1;

foreach ($experiment as $erow) {
	$did=$erow['dataset_id'];
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
	$aid=$erow['animal_id'];
	$cid=$erow['capture_id'];
	$eid=$erow['experiment_id'];
	$pid=$erow['phase_id'];
	$sid=$erow['setup_id'];
	$cdata=$db->query($sql_conddata,array($did,$sid)); $cdi=0;
	$edata=$db->query($sql_experimentdata,array($eid)); $edi=0;
	$pdata=$db->query($sql_phasedata,array($pid)); $pdi=0;
	for ($i = 0; $i < $dnum; $i++) {
		if ($colinfo[$i]['column_name']=="remark") {
			if ($colinfo[$i]['table_name']=="d_ori_experiment")
				$drec[$i]=$erow['experiment_remark'];
			else
				$drec[$i]=$erow['phase_remark'];
		} elseif ($colinfo[$i]['column_name']=="operator_id") {
			$drec[$i]=$erow['experiment_operator'];
		} elseif ($colinfo[$i]['table_name']=="p_ori_condition") {
			$drec[$i]=$cdata[$cdi++]['condition_value'];
		} elseif ($colinfo[$i]['table_name']=="d_ori_experiment_data") {
			$drec[$i]=$edata[$edi++]['data_value'];
		} elseif ($colinfo[$i]['table_name']=="d_ori_phase_data") {
			$drec[$i]=$pdata[$pdi++]['data_value'];
		} else {
			$drec[$i]=$erow[$colinfo[$i]['column_name']];
		}
	}
	add_record($xmldoc, $xmlnum, $xmlrec, $xmltype, $dnum, $drec, $dpos);
}

// display and quit
$xmldoc->display();
?>
