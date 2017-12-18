<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$data_id=$_POST['data_id'];
$header=$_POST['header'];
$keep_alias=$_POST['keep_alias'];
$remark=$_POST['remark'];

require_once $DBRoot."/lib/DBLink.php";

// SQL: insert new variable record
$sql="
insert into r_data_alias (
	data_id,
	header,
	remark,
	keep_alias
) values ($1,$2,$3,$4)
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Insert variable
if ($res = $db->execute($sql, array(
		$data_id,
		$header,
		$remark,
		$keep_alias))) {
	echo "<p>Variable alias created.</p>";
}

$db->disconnect();
?>
