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

// SQL statement for selection of all column definitions
$sql_column="
select
	p.header,
	r.column_name,
	r.data_type
from p_column p, r_data r
where p.data_id = r.data_id
  and p.dataset_id = $1
  and p.data_subset = 'trackpoint'
order by p.order_no
";

// Build SQL select statements for trackpoint records
$sql_trackpoint="
select
	a.dataset_id,
	a.animal,
	d.device,
	p.log_time,
	p.quality,
	p.latitude,
	p.longitude,
	p.speed,
	p.course,
	p.altitude
from d_gen_animal a, d_gen_track t, d_gen_device d, d_gen_trackpoint p
where a.animal_id = t.animal_id
  and t.track_id = d.track_id
  and d.device_id = p.device_id
  and a.dataset_id in (".implode(',',$dataset_arr).")
  and p.version = $1
order by a.dataset_id, a.animal, d.device, p.log_time
";

function add_record($xmldoc, $xmlnum, $xmlrec, $xmltype, $dnum, $drec, $dpos) {
	// Reset xml array values except Dataset Id, Animal Id and Device Id
	for ($i = 3; $i < $xmlnum; $i++)
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

$xmldoc = new XMLDocument("gen_trackpoint_pool.xml", "GEN Trackpoints");
$xmlnum = count($var_pos)+3;
$xmlrec = array();
$drec = array();

// Build header row in XML document
$xmldoc->add_row();
$xmldoc->add_cell ('Dataset Id', 'String');
$xmldoc->add_cell ('Animal Id', 'String');
$xmldoc->add_cell ('Device Id', 'String');
foreach ($var_arr as $var)
	$xmldoc->add_cell ($var, 'String');

// Retrieve data from database
$pres=pg_query_params($DB,$sql_trackpoint,array($ver))
	or die(pg_last_error()."<br/>");
$old_did=-1;

while ($prow=pg_fetch_assoc($pres)) {
	$did=$prow['dataset_id'];
	if ($did != $old_did) {
		// Get column information
		$colinfo=$db->query($sql_column,array($did));
		$dnum=count($colinfo);
		$xmltype=array_pad (array(), $xmlnum, NULL);
		$dpos=array_pad (array(), $dnum, -1);
		for ($i = 0; $i < $dnum; $i++) {
			if (array_key_exists ($colinfo[$i]['header'], $var_pos)) {
				$dpos[$i]=$var_pos[$colinfo[$i]['header']]+3;
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
	$xmlrec[1]=$prow['animal']; $xmltype[1]='String';
	$xmlrec[2]=$prow['device']; $xmltype[2]='String';
	for ($i = 0; $i < $dnum; $i++) {
		$drec[$i]=$prow[$colinfo[$i]['column_name']];
	}
	add_record($xmldoc, $xmlnum, $xmlrec, $xmltype, $dnum, $drec, $dpos);
}
pg_free_result($pres);
pg_close($DB);

// display and quit
$xmldoc->display();
?>
