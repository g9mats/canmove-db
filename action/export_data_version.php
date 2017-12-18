<?php
// Creator: Mats J Svensson, CAnMove

$dataset_id=$_POST['dataset_id'];
$data_subset=$_POST['data_subset'];
require_once "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";

// SQL: select variable sets
$sql="
select distinct f.version
from p_dataset d, l_file f
where d.dataset_id = f.dataset_id
  and d.dataset_id = $1
  and f.data_subset = $2
  and f.data_status = 'final'
  and f.loaded
order by f.version
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
echo '{"version":'.json_encode($arr).'}';
?>
