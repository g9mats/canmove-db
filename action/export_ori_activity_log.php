<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require $DBRoot."/lib/XMLDocument.php";
$did=$dataset_id;
$ver=$version;
if ($ver=="")
	$ver=1;

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
$sql_tz = "set time zone '".$tz."'";
$res = $db->execute($sql_tz);

// Log on to database using simple routine
require $DBRoot."/lib/DB.php";
$res = pg_query($DB,$sql_tz);

// Build SQL select statements for activity log records
$sql_column="
select
	p.order_no,
	p.header,
	r.data_type
from p_column p, r_data r
where p.data_id = r.data_id
  and p.dataset_id = $1
  and p.data_subset = $2
  and p.order_no in (COLUMN_LIST)
order by p.order_no
";

$sql_time="
select
	a.animal,
	c.capture_time,
	e.experiment_no,
	p.phase_no,
	l.time,
	l.order_no,
	l.data_value
from d_ori_animal a, d_ori_capture c, d_ori_experiment e, d_ori_phase p,
		d_ori_activity_log l
where a.animal_id = c.animal_id
  and c.capture_id = e.capture_id
  and e.experiment_id = p.experiment_id
  and p.phase_id = l.phase_id
  and a.dataset_id = $1
  and l.version = $2
  and l.order_no in (COLUMN_LIST)
order by a.animal, c.capture_time, e.experiment_no, p.phase_no,
			l.time, l.order_no
";

$xmldoc = new XMLDocument("ori_activity_log_".$did.".xml", "ORI Activity Log");
$xmlrec = array();
$data_type = array();

function add_record($xmldoc, $xmlrec, $data_type) {
	$xmldoc->add_row();
	for ($i = 0; $i < count($xmlrec); $i++) {
		if (($data_type[$i]=="integer") || ($data_type[$i]=="float"))
			$xmldoc->add_cell ($xmlrec[$i], 'Number');
		else
			$xmldoc->add_cell ($xmlrec[$i], 'String');
	}
}

$column_list = implode(',', $column_arr);
$sql_column = str_replace ("COLUMN_LIST", $column_list, $sql_column);
$sql_time = str_replace ("COLUMN_LIST", $column_list, $sql_time);

// Get column information and build header row in XML document
$xmldoc->add_row();
$data_type[0]="text";
$xmlrec[0]=NULL;
$xmldoc->add_cell ("Animal Id", 'String');
$data_type[1]="datetime";
$xmlrec[1]=NULL;
$xmldoc->add_cell ("Capture Time", 'String');
$data_type[2]="integer";
$xmlrec[2]=NULL;
$xmldoc->add_cell ("Experiment No", 'String');
$data_type[3]="integer";
$xmlrec[3]=NULL;
$xmldoc->add_cell ("Phase No", 'String');
$data_type[4]="float";
$xmlrec[4]=NULL;
$xmldoc->add_cell ("Time", 'String');
$colnum=5;
$varnum=0;
$res=$db->query($sql_column, array($did,$data_subset));
foreach ($res as $row) {
	$data_type[$colnum]=$row['data_type'];
	$xmlrec[$colnum]=NULL;
	$xmldoc->add_cell ($row['header'], 'String');
	$colnum++;
	$varnum++;
}

// Retrieve activity log from database
$res_time=pg_query_params($DB,$sql_time,array($did,$ver))
	or die(pg_last_error()."<br/>");
$vari=1;
while ($row_time = pg_fetch_assoc($res_time)) {
	if ($vari==1) {
		$xmlrec[0]=$row_time['animal'];
		$xmlrec[1]=$row_time['capture_time'];
		$xmlrec[2]=$row_time['experiment_no'];
		$xmlrec[3]=$row_time['phase_no'];
		$xmlrec[4]=$row_time['time'];
		$i=5;
	}
	$xmlrec[$i]=$row_time['data_value'];
	$i++;
	if ($vari==$varnum) {
		add_record($xmldoc, $xmlrec, $data_type);
		$vari=1;
	} else
		$vari++;
}
pg_free_result($res_time);
pg_close($DB);

// display and quit
$xmldoc->display();
?>
