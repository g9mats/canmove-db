<?php
/*
Creator: Mats J Svensson, CAnMove

This script deletes ORI capture data from database
(d_ori_animal, d_ori_animal_data, d_ori_capture, d_ori_capture_data).
*/
error_reporting (E_ALL);

function delete_data_ori_capture ($dataset_id) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL statement that checks for dependant experiment records
$sql_experiment="
select count(*) num
from l_file
where dataset_id = $1
  and data_subset = 'experiment'
  and data_status = 'final'
  and imported
";

// SQL statement that checks for dependant assessment records
$sql_assessment="
select count(*) num
from l_file
where dataset_id = $1
  and data_subset = 'assessment'
  and data_status = 'final'
  and imported
";

// SQL statement that deletes all capture data from database
$sql_capture_data="
delete from d_ori_capture_data
where capture_id in (
	select capture_id
	from d_ori_capture
	where animal_id in (
		select animal_id
		from d_ori_animal
		where dataset_id = $1
		)
	)
";

// SQL statement that deletes all captures from database
$sql_capture="
delete from d_ori_capture
where animal_id in (
	select animal_id
	from d_ori_animal
	where dataset_id = $1
	)
";

// SQL statement that deletes all animal data from database
$sql_animal_data="
delete from d_ori_animal_data
where animal_id in (
	select animal_id
	from d_ori_animal
	where dataset_id = $1
	)
";

// SQL statement that deletes all animals from database
$sql_animal="
delete from d_ori_animal
where dataset_id = $1
";

$res=$db->query($sql_experiment, array($dataset_id));
$edata=$res[0]['num'];
$res=$db->query($sql_assessment, array($dataset_id));
$adata=$res[0]['num'];
if (($edata>0)||($adata>0)) {
	echo "<p>You can not delete capture data:<br/>";
	if ($edata>0)
		echo "- There are still experiment data left.<br/>";
	if ($adata>0)
		echo "- There are still assessment data left.<br/>";
	echo "</p>";
	return 1;
}

$res=$db->execute($sql_capture_data, array($dataset_id));
$res=$db->execute($sql_capture, array($dataset_id));
$res=$db->execute($sql_animal_data, array($dataset_id));
$res=$db->execute($sql_animal, array($dataset_id));

return 0;
}
?>
