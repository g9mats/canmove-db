<?php
// Creator: Mats J Svensson, CAnMove

$table_name=$_POST['table_name'];
require_once "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";

// SQL: select data subsets
$sql="
select column_name
from information_schema.columns
where table_name = $1
order by column_name
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$arr=array();

if ($res = $db->query($sql, array($table_name))) {
	$i=0;
	foreach ($res as $row)
		$arr[$i++]=$row;
}
//header("Content-Type: application/json; charset=UTF-8");
echo '{"column_name":'.json_encode($arr).'}';
?>
