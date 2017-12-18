<?php
// Creator: Mats J Svensson, CAnMove

$dataset_id=$_POST['dataset_id'];
if ($dataset_id=="") {
	echo '{"errmsg":"You must specify a dataset."}';
	return;
}
$name=$_POST['name'];
if ($name=="") {
	echo '{"errmsg":"You must specify a name."}';
	return;
}
$remark=$_POST['remark'];
require_once "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";

// SQL: check for taxon in dataset table
$sql_check1="
select itis_tsn
from p_taxon
where dataset_id = $1
  and taxon = $2
";

// SQL: select tsn from taxon reference table
$sql_check2="
select tsn
from r_taxon
where complete_name = $1
order by name_usage desc
";

// SQL: insert taxon into dataset table
$sql_insert="
insert into p_taxon (
	dataset_id,
	itis_tsn,
	taxon,
	remark
	) values ($1, $2, $3, $4)
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

if ($res = $db->query($sql_check1, array($dataset_id,$name))) {
	echo '{"errmsg":"Taxon already exists."}';
	return;
}
if (!$res = $db->query($sql_check2, array($name))) {
	echo '{"errmsg":"Not a valid taxon."}';
	return;
}
if ($res2 = $db->execute($sql_insert,
		array($dataset_id,$res[0]['tsn'],$name,$remark))) {
	echo '{"errmsg":""}';
} else {
	echo '{"errmsg":"Unable to insert taxon."}';
}
?>
