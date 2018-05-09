<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require $DBRoot."/lib/XMLDocument.php";
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
	p.header,
	r.data_type
from p_column p, r_data r
where p.data_id = r.data_id
  and p.dataset_id = $1
  and p.data_subset = 'activity_log'
  and p.header in ('".implode("','",$var_arr)."')
order by p.order_no
";

$sql_time="
select
	x.dataset_id,
	a.animal,
	c.capture_time,
	e.experiment_no,
	p.phase_no,
	l.time,
	l.data_value
from p_column x, d_ori_animal a, d_ori_capture c, d_ori_experiment e,
		d_ori_phase p, d_ori_activity_log l
where x.dataset_id = a.dataset_id
  and a.animal_id = c.animal_id
  and c.capture_id = e.capture_id
  and e.experiment_id = p.experiment_id
  and p.phase_id = l.phase_id
  and x.order_no = l.order_no
  and x.dataset_id in (".implode(',',$dataset_arr).")
  and x.header in ('".implode("','",$var_arr)."')
  and l.version = $1
order by x.dataset_id, a.animal, c.capture_time, e.experiment_no, p.phase_no,
		l.time, l.order_no
";

function add_record($xmldoc, $xmlnum, $xmlrec, $xmltype, $dnum, $drec, $dpos) {
	// Reset xml array values except Dataset Id, Animal Id,
	//		Capture Time, Experiment No and Phase No
	for ($i = 6; $i < $xmlnum; $i++)
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

$xmldoc = new XMLDocument("ori_activity_log_pool.xml", "ORI Activity Log");
$xmlnum = count($var_pos)+6;
$xmlrec = array();
$drec = array();

// Build header row in XML document
$xmldoc->add_row();
$xmldoc->add_cell ("Dataset Id", 'String');
$xmldoc->add_cell ("Animal Id", 'String');
$xmldoc->add_cell ("Capture Time", 'String');
$xmldoc->add_cell ("Experiment No", 'String');
$xmldoc->add_cell ("Phase No", 'String');
$xmldoc->add_cell ("Time", 'String');
foreach ($var_arr as $var)
	$xmldoc->add_cell ($var, 'String');

// Retrieve activity log from database
$dres=pg_query_params($DB,$sql_time,array($ver))
	or die(pg_last_error()."<br/>");
$old_did=-1;
$di=0;

while ($drow = pg_fetch_assoc($dres)) {
	$did=$drow['dataset_id'];
	if ($did != $old_did) {
		// Get column information
		$colinfo=$db->query($sql_column,array($did));
		$dnum=count($colinfo);
		$xmltype=array_pad (array(), $xmlnum, NULL);
		$dpos=array_pad (array(), $dnum, -1);
		for ($i = 0; $i < $dnum; $i++) {
			$dpos[$i]=$var_pos[$colinfo[$i]['header']]+6;
			if (in_array($colinfo[$i]['data_type'],array("integer","float")))
				$xmltype[$dpos[$i]]='Number';
			else
				$xmltype[$dpos[$i]]='String';
		}
		$xmlrec[0]=$did;
		$xmltype[0]='Number';
		$old_did=$did;
	}
	if ($di==0) {
		$xmlrec[1]=$drow['animal']; $xmltype[1]='String';
		$xmlrec[2]=$drow['capture_time']; $xmltype[2]='String';
		$xmlrec[3]=$drow['experiment_no']; $xmltype[3]='Number';
		$xmlrec[4]=$drow['phase_no']; $xmltype[4]='Number';
		$xmlrec[5]=$drow['time']; $xmltype[5]='Number';
	}
	$drec[$di]=$drow['data_value'];
	$di++;
	if ($di==$dnum) {
		add_record($xmldoc, $xmlnum, $xmlrec, $xmltype, $dnum, $drec, $dpos);
		$di=0;
	}
}
pg_free_result($dres);
pg_close($DB);

// display and quit
$xmldoc->display();
?>
