<?php
// Creator: Mats J Svensson, CAnMove

$dataset_id=$_POST['dataset_id'];
require_once "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";

// SQL: select taxa
$sql="
select
	taxon_id,
	taxon,
	case remark is null
		when true then ''
		else remark
	end as remark
from p_taxon
where dataset_id = $1
order by taxon
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
echo '{"taxon":'.json_encode($arr).'}';
?>
