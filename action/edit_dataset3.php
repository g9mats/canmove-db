<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
$title=$_POST['title'];
$data_type=$_POST['data_type'];
$method=$_POST['method'];
$site=$_POST['site'];
$start_date=$_POST['start_date'];
$end_date=$_POST['end_date'];
$animal_num=$_POST['animal_num'];
$track_num=$_POST['track_num'];
$animal_db=$_POST['animal_db'];
$track_db=$_POST['track_db'];
$data_location=$_POST['data_location'];
$contact_id=$_POST['contact_id'];
$public=$_POST['public'];
$release_date=$_POST['release_date'];
$remark=$_POST['remark'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
if ($title=="") {
	echo "<p>You must specify a dataset title.</p>";
	return;
}
if ($start_date=="") {
	$start_date=null;
}
if ($end_date=="") {
	$end_date=null;
}
if ($animal_num=="") {
	$animal_num=null;
}
if ($track_num=="") {
	$track_num=null;
}
if ($animal_db=="") {
	$animal_db=null;
}
if ($track_db=="") {
	$track_db=null;
}
if ($contact_id=="") {
	$contact_id=null;
}
if ($release_date=="") {
	$release_date=null;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: update dataset
$sql="
update p_dataset set
	title = $2,
	data_type = $3,
	method = $4,
	site = $5,
	start_date = $6,
	end_date = $7,
	animal_num = $8,
	track_num = $9,
	animal_db = $10,
	track_db = $11,
	data_location = $12,
	contact_id = $13,
	public = $14,
	release_date = $15,
	remark = $16
where dataset_id = $1
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<?php
// Update dataset
if ($res = $db->execute($sql,array($dataset_id,$title,$data_type,
			$method,$site,$start_date,$end_date,
			$animal_num,$track_num,$animal_db,$track_db,
			$data_location,$contact_id,$public,$release_date,$remark))) {
	echo "<p>Dataset updated.</p>";
}
?>

<?php $db->disconnect(); ?>
