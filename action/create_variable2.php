<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$storage_type=$_POST['storage_type'];
$data_subset=$_POST['data_subset'];
$table_name=$_POST['table_name'];
$column_name=$_POST['column_name'];
$column_type=$_POST['column_type'];
$data_type=$_POST['data_type'];
$case_type=$_POST['case_type'];
$mandatory=$_POST['mandatory'];
$nullable=$_POST['nullable'];
$load_name=$_POST['load_name'];
$header=$_POST['header'];
$unit=$_POST['unit'];
$remark=$_POST['remark'];

require_once $DBRoot."/lib/DBLink.php";

// SQL: insert new variable record
$sql="
insert into r_data (
	storage_type,
	data_subset,
	table_name,
	column_name,
	column_type,
	data_type,
	case_type,
	mandatory,
	nullable,
	load_name,
	header,
	unit,
	remark
) values ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13)
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Insert variable
if ($res = $db->execute($sql, array(
		$storage_type,
		$data_subset,
		$table_name,
		$column_name,
		$column_type,
		$data_type,
		$case_type,
		$mandatory,
		$nullable,
		$load_name,
		$header,
		$unit,
		$remark))) {
	echo "<p>Variable created.</p>";
}

$db->disconnect();
?>
