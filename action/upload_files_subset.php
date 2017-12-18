<?php
// Creator: Mats J Svensson, CAnMove

$dataset_id=$_POST['dataset_id'];
require_once "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";

// SQL: select data subsets
$sql="
select s.order_no, s.data_subset, s.subset_name
from p_dataset d, r_data_subset s, x_context x
where d.storage_type = s.storage_type
  and d.dataset_id = $1
  and x.context_type = 'action'
  and x.context_key = 'upload_files'
  and x.object_type = 'storage_type'
  and x.object_key = d.storage_type
union
select s.order_no, s.data_subset, s.subset_name
from p_dataset d, r_data_subset s, x_context x
where d.storage_type = s.storage_type
  and d.dataset_id = $1
  and x.context_type = 'action'
  and x.context_key = 'upload_files'
  and x.object_type = 'data_subset'
  and x.object_key = d.storage_type
  and x.object_key2 = s.data_subset
order by order_no
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
