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
  and p.data_subset = 'count'
order by p.order_no
";

// SQL statement for selection of all counts
$sql_count="
select
	a.dataset_id,
	a.animal_id,
	a.animal,
	c.capture_id,
	c.capture_time,
	e.experiment_no,
	p.phase_no,
	d.count_id,
	d.funnel_line,
	d.operator_id,
	d.northern_sector,
	d.s1_direction,
	d.activity,
	d.d1,
	d.s,
	d.r1,
	d.p1,
	d.d2a,
	d.d2b,
	d.r2,
	d.p2,
	d.direction,
	d.remark
from d_ori_animal a, d_ori_capture c, d_ori_experiment e, d_ori_phase p,
	d_ori_count d
where a.animal_id = c.animal_id
  and a.animal_id = e.animal_id
  and c.capture_id = e.capture_id
  and e.experiment_id = p.experiment_id
  and p.phase_id = d.phase_id
  and a.dataset_id in (".implode(',',$dataset_arr).")
order by a.dataset_id, a.animal, c.capture_time, e.experiment_no, p.phase_no
";

$sql_sector="
select
	amount
from d_ori_sector
where count_id = $1
order by sector
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

$xmldoc = new XMLDocument("ori_count_pool.xml", "ORI Counts");
$xmlnum = count($var_pos)+1;
$xmlrec = array();
$drec = array();

// Build header row in XML document
$xmldoc->add_row();
$xmldoc->add_cell ('Dataset Id', 'String');
foreach ($var_arr as $var)
	$xmldoc->add_cell ($var, 'String');

// Retrieve data from database
$count=$db->query($sql_count,array());
$old_did=-1;

foreach ($count as $crow) {
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
	$cid=$crow['count_id'];
	$sdata=$db->query($sql_sector,array($cid)); $sdi=0;
	for ($i = 0; $i < $dnum; $i++) {
		if ($colinfo[$i]['table_name']=="d_ori_sector") {
			$drec[$i]=$sdata[$sdi++]['amount'];
		} else {
			$drec[$i]=$crow[$colinfo[$i]['column_name']];
		}
	}
	add_record($xmldoc, $xmlnum, $xmlrec, $xmltype, $dnum, $drec, $dpos);
}

// display and quit
$xmldoc->display();
?>
