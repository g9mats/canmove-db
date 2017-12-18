<?php
// Creator: Mats J Svensson, CAnMove

$storage_type=$_POST['storage_type'];
require_once "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";

// SQL: select data subsets
$sql="
select
	data_subset,
	subset_name
from r_data_subset
where storage_type = $1
order by order_no
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$arr=array();

if ($res = $db->query($sql, array($storage_type))) {
	$i=0;
	foreach ($res as $row)
		$arr[$i++]=$row;
}
//header("Content-Type: application/json; charset=UTF-8");
echo '{"data_subset":'.json_encode($arr).'}';
?>
