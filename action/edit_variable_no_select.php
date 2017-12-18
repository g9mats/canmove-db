<?php
// Creator: Mats J Svensson, CAnMove

$storage_type=$_POST['storage_type'];
$data_subset=$_POST['data_subset'];
require_once "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";

// SQL: select variables
$sql_var="
select
	d.data_id,
	h.table_header,
	h.order_no as h_order_no,
	case d.mandatory
		when true then 'Mandatory'
		else 'Optional'
	end mandatory,
	d.header,
	d.order_no
from r_data d, x_table_header h
where d.table_name = h.table_name
  and d.storage_type = $1
  and d.data_subset = $2
order by order_no, h_order_no, mandatory, header
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$arr=array();

if ($res = $db->query($sql_var, array($storage_type,$data_subset))) {
	$i=0;
	foreach ($res as $row)
		$arr[$i++]=$row;
}
//header("Content-Type: application/json; charset=UTF-8");
echo '{"vararr":'.json_encode($arr).'}';
?>
