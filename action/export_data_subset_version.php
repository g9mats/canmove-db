<?php
// Creator: Mats J Svensson, CAnMove

$dataset_id=$_POST['dataset_id'];
$data_subset=$_POST['data_subset'];
require_once "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";

// SQL: select data subsets
$sql="
select versions
from p_dataset d, r_data_subset s
where d.storage_type = s.storage_type
  and d.dataset_id = $1
  and s.data_subset = $2
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$arr=array();

if ($res = $db->query($sql, array($dataset_id,$data_subset))) {
	$i=0;
	foreach ($res as $row)
		$arr[$i++]=$row;
}
//header("Content-Type: application/json; charset=UTF-8");
echo '{"versions":'.json_encode($arr).'}';
?>
