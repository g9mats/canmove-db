<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$data_alias_id=$_POST['data_alias_id'];
$data_id=$_POST['data_id'];
$header=$_POST['header'];
$keep_alias=$_POST['keep_alias'];
$remark=$_POST['remark'];
require_once $DBRoot."/lib/DBLink.php";

// SQL: update variable record
$sql="
update r_data_alias set
	data_id = $2,
	header = $3,
	remark = $4,
	keep_alias = $5
where data_alias_id = $1
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Update variable
if ($res = $db->execute($sql,
		array(
			$data_alias_id,
			$data_id,
			$header,
			$remark,
			$keep_alias
			))) {
	echo "<p>Variable alias updated.</p>";
}

$db->disconnect();
?>
