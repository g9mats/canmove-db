<?php
// Creator: Mats J Svensson, CAnMove

$data_id=$_POST['data_id'];
if ($data_id=="") {
	echo '{"errmsg":"You must specify a data_id."}';
	return;
}
$order_no=$_POST['order_no'];
if ($order_no=="") {
	$order_no=NULL;
}
require_once "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";

// SQL: update order no in variable table
$sql_update="
update r_data set
	order_no = $2
where data_id = $1
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

if ($res = $db->execute($sql_update,
		array($data_id,$order_no))) {
	echo '{"errmsg":""}';
} else {
	echo '{"errmsg":"Unable to update order no."}';
}
?>
