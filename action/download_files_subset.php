<?php
// Creator: Mats J Svensson, CAnMove

$dataset_id=$_POST['dataset_id'];
require_once "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";

// SQL: select data subsets
$sql="
select distinct r.order_no, r.data_subset, r.subset_name
from p_dataset d, l_file f, r_data_subset r
where d.dataset_id = f.dataset_id
  and f.data_subset = r.data_subset
  and d.storage_type = r.storage_type
  and f.dataset_id = $1
  and not f.deleted
order by r.order_no
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$arr=array();

if ($res = $db->query($sql, array($dataset_id))) {
	$i=0;
	foreach ($res as $row)
		$arr[$i++]=$row;
}
//header("Content-Type: application/json; charset=UTF-8");
echo '{"data_subset":'.json_encode($arr).'}';
?>
