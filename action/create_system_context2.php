<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$context_type=$_POST['context_type'];
$context_key=$_POST['context_key'];
$object_type=$_POST['object_type'];
$object_key=$_POST['object_key'];
$object_key2=$_POST['object_key2'];
$order_no=$_POST['order_no'];
if ($order_no=="") $order_no=NULL;

require_once $DBRoot."/lib/DBLink.php";

// SQL: insert new context record
$sql="
insert into x_context (
	context_type,
	context_key,
	object_type,
	object_key,
	object_key2,
	order_no
) values ($1,$2,$3,$4,$5,$6)
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Insert context
if ($res = $db->execute($sql, array(
		$context_type,
		$context_key,
		$object_type,
		$object_key,
		$object_key2,
		$order_no))) {
	echo "<p>Context entry created.</p>";
}

$db->disconnect();
?>
