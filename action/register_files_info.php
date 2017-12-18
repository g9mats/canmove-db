<?php
// Creator: Mats J Svensson, CAnMove

$file_id=$_POST['file_id'];
require_once "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL: select data subsets
$sql="
select device_id, period, version, varset, remark
from l_file
where file_id = $1
";

if ($res = $db->query($sql, array($file_id))) {
	$row = $res[0];
	$device_id = $row['device_id'];
	$period = $row['period'];
	$version = $row['version'];
	$varset = $row['varset'];
	$remark = $row['remark'];
} else {
	$device_id = "";
	$period = "";
	$version = "";
	$varset = "";
	$remark = "";
}
//header("Content-Type: application/json; charset=UTF-8");
echo '{"device_id":"'.$device_id.'",';
echo '"period":"'.$period.'",';
echo '"version":"'.$version.'",';
echo '"varset":"'.$varset.'",';
echo '"remark":"'.$remark.'"}';
?>
