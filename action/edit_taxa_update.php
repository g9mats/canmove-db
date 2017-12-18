<?php
// Creator: Mats J Svensson, CAnMove

$taxon_id=$_POST['taxon_id'];
if ($taxon_id=="") {
	echo '{"errmsg":"You must specify a taxon_id."}';
	return;
}
$dataset_id=$_POST['dataset_id'];
if ($dataset_id=="") {
	echo '{"errmsg":"You must specify a dataset_id."}';
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
  and taxon_id <> $3
";

// SQL: select tsn from taxon reference table
$sql_check2="
select tsn
from r_taxon
where complete_name = $1
order by name_usage desc
";

// SQL: update taxon in dataset table
$sql_update="
update p_taxon set
	itis_tsn = $2,
	taxon = $3,
	remark = $4
where taxon_id = $1
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

if ($res = $db->query($sql_check1, array($dataset_id,$name,$taxon_id))) {
	echo '{"errmsg":"Taxon already exists."}';
	return;
}
if (!$res = $db->query($sql_check2, array($name))) {
	echo '{"errmsg":"Not a valid taxon."}';
	return;
}
if ($res2 = $db->execute($sql_update,
		array($taxon_id,$res[0]['tsn'],$name,$remark))) {
	echo '{"errmsg":""}';
} else {
	echo '{"errmsg":"Unable to update taxon."}';
}
?>
