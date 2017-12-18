<?php
// Creator: Mats J Svensson, CAnMove

$file_id=$_POST['file_id'];
require_once "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL: select data subsets
$sql="
select remark
from l_file
where file_id = $1
";

if ($res = $db->query($sql, array($file_id))) {
	$remark = $res[0]['remark'];
} else {
	$remark = "";
}
//header("Content-Type: application/json; charset=UTF-8");
echo '{"remark":"'.$remark.'"}';
?>
