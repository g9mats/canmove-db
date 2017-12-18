<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$project_id=$_POST['project_id'];
$title=$_POST['title'];
$owner_id=$_POST['owner_id'];
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
if ($project_id=="") {
	echo "<p>You must specify a project.</p>";
	return;
}
if ($title=="") {
	echo "<p>You must specify a title.</p>";
	return;
}
if ($owner_id=="") {
	echo "<p>You must specify a owner.</p>";
	return;
}
if ($data_type=="") {
	echo "<p>You must specify a data type.</p>";
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

// SQL: select the next dataset_id and the current user_id
$sql_ids="
select
	nextval('p_dataset_dataset_id_seq') dataset_id,
	person_id
from r_person
where drupal_id = $1
";

// SQL: select storage_type for current data_type
$sql_storage_type="
select
	storage_type
from r_data_type
where data_type = $1
";

// SQL: insert new dataset record
$sql_dataset="
insert into p_dataset (
	dataset_id, project_id, title, data_type, storage_type,
	method, site, start_date, end_date,
	animal_num, track_num, animal_db, track_db,
	data_location, contact_id, public, release_date, remark
	)
values ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10,
	$11, $12, $13, $14, $15, $16, $17, $18)
";

// SQL: insert new dataset role record
$sql_role="
insert into p_dataset_role (dataset_id, user_id, user_role)
values ($1, $2, $3)
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Get variable information and present it in a table
if ($resI = $db->query($sql_ids, array($user->uid))) {
	$dataset_id = $resI[0]['dataset_id'];
	$user_id = $resI[0]['person_id'];
	$resS = $db->query($sql_storage_type, array($data_type));
	$storage_type = $resS[0]['storage_type'];
	$resD = $db->execute($sql_dataset,
		array($dataset_id,$project_id,$title,$data_type,$storage_type,
			$method, $site, $start_date, $end_date,
			$animal_num, $track_num, $animal_db, $track_db,
			$data_location, $contact_id, $public, $release_date, $remark));
	$resA = $db->execute($sql_role, array($dataset_id,$owner_id,"O"));
	if ($user_id <> $owner_id)
		$resA = $db->execute($sql_role, array($dataset_id,$user_id,"A"));
	echo "<p>New dataset created.</p>";
}

$db->disconnect();
?>
