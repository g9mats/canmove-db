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

// Log on to database using simple routine
require $DBRoot."/lib/DB.php";

// Build SQL select statements for datapoint records
$sql_column="
select
	p.header,
	r.data_type
from p_column p, r_data r
where p.data_id = r.data_id
  and p.dataset_id = $1
  and p.data_subset = 'datapoint'
  and p.header in ('".implode("','",$var_arr)."')
order by p.order_no
";

$sql_time="
select
	c.dataset_id,
	a.animal,
	d.device,
	p.log_time,
	p.data_value
from p_column c, d_gen_animal a, d_gen_track t, d_gen_device d,
	d_gen_datapoint p
where c.dataset_id = a.dataset_id
  and a.animal_id = t.animal_id
  and t.track_id = d.track_id
  and d.device_id = p.device_id
  and c.order_no = p.order_no
  and c.dataset_id in (".implode(',',$dataset_arr).")
  and c.header in ('".implode("','",$var_arr)."')
  and p.version = $1
order by c.dataset_id, a.animal, d.device, p.log_time, p.order_no
";

function add_record($xmldoc, $xmlnum, $xmlrec, $xmltype, $dnum, $drec, $dpos) {
	// Reset xml array values except Dataset Id, Animal Id,
	//		Device Id and Log Time
	for ($i = 4; $i < $xmlnum; $i++)
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

$xmldoc = new XMLDocument("gen_datapoint_pool.xml", "GEN Datapoints");
$xmlnum = count($var_pos)+4;
$xmlrec = array();
$drec = array();

// Build header row in XML document
$xmldoc->add_row();
$xmldoc->add_cell ("Dataset Id", 'String');
$xmldoc->add_cell ("Animal Id", 'String');
$xmldoc->add_cell ("Device Id", 'String');
$xmldoc->add_cell ("Log Time", 'String');
foreach ($var_arr as $var)
	$xmldoc->add_cell ($var, 'String');

// Retrieve datapoints from database
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
			$dpos[$i]=$var_pos[$colinfo[$i]['header']]+4;
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
		$xmlrec[2]=$drow['device']; $xmltype[2]='String';
		$xmlrec[3]=$drow['log_time']; $xmltype[3]='String';
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
