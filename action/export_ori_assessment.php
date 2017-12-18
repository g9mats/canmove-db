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
  and p.data_subset = 'assessment'
order by p.order_no
";

// SQL statement for selection of all assessments
$sql_assessment="
select
	a.animal_id,
	a.animal,
	c.capture_id,
	c.capture_time,
	e.assessment_id,
	e.assessment_no,
	e.assessment_time,
	e.operator_id,
	e.remark
from d_ori_animal a, d_ori_capture c, d_ori_assessment e
where a.animal_id = c.animal_id
  and a.animal_id = e.animal_id
  and c.capture_id = e.capture_id
  and a.dataset_id = $1
order by a.animal, c.capture_time, e.assessment_no
";

$sql_assessmentdata="
select
	data_value
from d_ori_assessment_data
where assessment_id = $1
order by order_no
";

$xmldoc = new XMLDocument("ori_assessment_".$did.".xml", "ORI Assessments");
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
$assessment=$db->query($sql_assessment,array($did));

foreach ($assessment as $arow) {
	$aid=$arow['animal_id'];
	$cid=$arow['capture_id'];
	$assid=$arow['assessment_id'];
	$assdata=$db->query($sql_assessmentdata,array($assid)); $adi=0;
	for ($i = 0; $i < count($colinfo); $i++) {
		if ($colinfo[$i]['table_name']=="d_ori_assessment_data") {
			$xmlrec[$i]=$assdata[$adi++]['data_value'];
		} else {
			$xmlrec[$i]=$arow[$colinfo[$i]['column_name']];
		}
	}
	add_record($xmldoc, $xmlrec, $colinfo);
}

// display and quit
$xmldoc->display();
?>
