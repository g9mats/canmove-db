<?php
/*
Creator: Mats J Svensson, CAnMove

This script deletes ORI assessment data from database
(d_ori_assessment, d_ori_assessment_data).
*/
error_reporting (E_ALL);

function delete_data_ori_assessment ($dataset_id) {

require "./canmove.inc";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL statement that deletes all assessment data from database
$sql_assessment_data="
delete from d_ori_assessment_data
where assessment_id in (
	select assessment_id
	from d_ori_assessment
	where animal_id in (
		select animal_id
		from d_ori_animal
		where dataset_id = $1
		)
	)
";

// SQL statement that deletes all assessments from database
$sql_assessment="
delete from d_ori_assessment
where animal_id in (
	select animal_id
	from d_ori_animal
	where dataset_id = $1
	)
";

$res=$db->execute($sql_assessment_data, array($dataset_id));
$res=$db->execute($sql_assessment, array($dataset_id));

return 0;
}
?>
