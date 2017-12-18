<?php
// Creator: Mats J Svensson, CAnMove

$dataset_id=$_POST['dataset_id'];
require_once "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";

// SQL: select data subsets
$sql="
select distinct s.order_no, s.data_subset, s.subset_name
from p_dataset d, l_file f, r_data_subset s
where d.dataset_id = f.dataset_id
  and d.storage_type = s.storage_type
  and f.data_subset = s.data_subset
  and d.dataset_id = $1
  and f.data_status = 'final'
  and f.imported
  and not f.validated
order by s.order_no
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
