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
  and p.data_subset = 'count'
order by p.order_no
";

// SQL statement for selection of all counts
$sql_count="
select
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
  and a.dataset_id = $1
order by a.animal, c.capture_time, e.experiment_no, p.phase_no
";

$sql_sector="
select
	amount
from d_ori_sector
where count_id = $1
order by sector
";

$xmldoc = new XMLDocument("ori_count_".$did.".xml", "ORI Counts");
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
$count=$db->query($sql_count,array($did));

foreach ($count as $crow) {
	$cid=$crow['count_id'];
	$sdata=$db->query($sql_sector,array($cid)); $sdi=0;
	for ($i = 0; $i < count($colinfo); $i++) {
		if ($colinfo[$i]['table_name']=="d_ori_sector") {
			$xmlrec[$i]=$sdata[$sdi++]['amount'];
		} else {
			$xmlrec[$i]=$crow[$colinfo[$i]['column_name']];
		}
	}
	add_record($xmldoc, $xmlrec, $colinfo);
}

// display and quit
$xmldoc->display();
?>
