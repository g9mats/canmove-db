<?php
// Creator: Mats J Svensson, CAnMove

$dataset_id=$_POST['dataset_id'];
$data_subset=$_POST['data_subset'];
require_once "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";

// SQL: select data subsets
$sql="
select distinct data_status, initcap(data_status) as status_name
from l_file
where dataset_id = $1
  and data_subset = $2
  and not deleted
order by data_status
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
echo '{"data_status":'.json_encode($arr).'}';
?>
