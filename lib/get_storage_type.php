<?php
// Creator: Mats J Svensson, CAnMove

require_once $DBRoot."/lib/DB.php";

$sql="
select
	lower(storage_type) storage_type
from p_dataset
where dataset_id = $1
";

$res=pg_query_params($DB,$sql,array($dataset_id));
if ($res) {
	$row=pg_fetch_assoc($res);
	$storage_type=$row['storage_type'];
}
pg_free_result($res);
?>
