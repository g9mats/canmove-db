<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
$answer=$_POST['answer'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
if ($answer=="") {
	echo "<p>You must specify an answer.</p>";
	return;
}
if ($answer=="N") {
	echo "<p>Dataset not deleted.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: select data
$sql_data="
select count(*) as dnum
from l_file
where dataset_id = $1
  and loaded_data
";

// SQL: select files
$sql_file="
select count(*) as fnum
from l_file
where dataset_id = $1
  and not deleted
";

// SQL: delete file log entries
$sql_delfile_log="
delete from l_file_log
where file_id in (
	select file_id
	from l_file
	where dataset_id = $1
	)
";

// SQL: delete files
$sql_delfile="
delete from l_file
where dataset_id = $1
";

// SQL: delete taxa
$sql_taxon="
delete from p_taxon
where dataset_id = $1
";

// SQL: delete roles
$sql_role="
delete from p_dataset_role
where dataset_id = $1
";

// SQL: delete dataset
$sql_dataset="
delete from p_dataset
where dataset_id = $1
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$res=$db->query($sql_data,array($dataset_id));
$dnum=$res[0]['dnum'];
$res=$db->query($sql_file,array($dataset_id));
$fnum=$res[0]['fnum'];
if (($dnum>0)||($fnum>0)) {
	echo "You can not delete dataset:<br/>";
	if ($dnum>0)
		echo "- There are still data in the database.<br/>";
	if ($fnum>0)
		echo "- There are still uploaded files left.<br/>";
} else {
	$res = $db->execute($sql_delfile_log,array($dataset_id));
	$res = $db->execute($sql_delfile,array($dataset_id));
	$res = $db->execute($sql_taxon,array($dataset_id));
	$res = $db->execute($sql_role,array($dataset_id));
	$res = $db->execute($sql_dataset,array($dataset_id));
	echo "<p>Dataset deleted.</p>";
}

$db->disconnect();
?>
