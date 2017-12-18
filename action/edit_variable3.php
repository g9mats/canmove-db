<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$data_id=$_POST['data_id'];
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
$order_no=$_POST['order_no'];
if ($order_no=="")
	$order_no=NULL;
$unit=$_POST['unit'];
$remark=$_POST['remark'];
require_once $DBRoot."/lib/DBLink.php";

// SQL: update variable record
$sql="
update r_data set
	storage_type = $2,
	data_subset = $3,
	table_name = $4,
	column_name = $5,
	column_type = $6,
	data_type = $7,
	case_type = $8,
	mandatory = $9,
	nullable = $10,
	load_name = $11,
	header = $12,
	order_no = $13,
	unit = $14,
	remark = $15
where data_id = $1
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Update variable
if ($res = $db->execute($sql,
		array(
			$data_id,
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
			$order_no,
			$unit,
			$remark
			))) {
	echo "<p>Variable updated.</p>";
}

$db->disconnect();
?>
