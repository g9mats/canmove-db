<?php
// Creator: Mats J Svensson, CAnMove

$storage_type=$_POST['storage_type'];
require_once "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";

// SQL: select table names for a certain storage type
$sql="
select object_key as table_name
from x_context
where context_type='storage_type'
  and context_key = $1
  and object_type = 'table_name'
order by object_key
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
echo '{"table_name":'.json_encode($arr).'}';
?>
