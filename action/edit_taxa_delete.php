<?php
// Creator: Mats J Svensson, CAnMove

$taxon_id=$_POST['taxon_id'];
if ($taxon_id=="") {
	echo '{"errmsg":"You must specify a taxon_id."}';
	return;
}
require_once "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";

// SQL: delete taxon from dataset table
$sql="
delete from p_taxon
where taxon_id = $1
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

if ($res = $db->execute($sql, array($taxon_id))) {
	echo '{"errmsg":""}';
} else {
	echo '{"errmsg":"Unable to delete taxon."}';
}
?>
