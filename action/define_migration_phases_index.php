<?php
// Creator: Mats J Svensson, CAnMove

$device_id=$_POST['device_id'];
$version=$_POST['version'];
$phase_type=$_POST['phase_type'];
require_once "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";

// SQL: select data subsets
$sql="
select max(phase_index) maxidx
from d_gen_migration_phase
where device_id = $1
  and version = $2
  and phase_type = $3
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

if ($res = $db->query($sql, array($device_id,$version,$phase_type))) {
	$idx = $res[0]['maxidx'] + 1;
} else
	$idx = 1;
//header("Content-Type: application/json; charset=UTF-8");
echo '{"phase_index":'.$idx.'}';
?>
