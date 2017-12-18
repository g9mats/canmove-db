<?php
// Creator: Mats J Svensson, CAnMove

$dataset_id=$_POST['dataset_id'];
$data_subset=$_POST['data_subset'];
$data_status=$_POST['data_status'];
require_once "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";

// SQL: select data subsets
$sql="
select file_id, original_name, upload_time
from l_file f
where dataset_id = $1
  and data_subset = $2
  and data_status = $3
  and not deleted
order by original_name
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$arr=array();

if ($res = $db->query($sql, array($dataset_id,$data_subset,$data_status))) {
	$i=0;
	foreach ($res as $row)
		$arr[$i++]=$row;
}
//header("Content-Type: application/json; charset=UTF-8");
echo '{"file":'.json_encode($arr).'}';
?>
