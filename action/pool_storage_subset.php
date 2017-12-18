<?php
// Creator: Mats J Svensson, CAnMove

$uid=$_POST['uid'];
$storage_type=$_POST['storage_type'];
require_once "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";

// SQL: select data subsets
$sql="
select distinct
	r.order_no,
	r.data_subset,
	r.subset_name
from p_dataset d, p_dataset_role a, r_person p, l_file f, r_data_subset r
where d.dataset_id = a.dataset_id
  and a.user_id = p.person_id
  and d.storage_type in (
	select object_key from x_context
	where context_type = 'action'
	  and context_key = 'pool_data'
	  and object_type = 'storage_type'
  )
  and d.dataset_id = f.dataset_id
  and d.storage_type = r.storage_type
  and f.data_subset = r.data_subset
  and f.data_status = 'final'
  and f.loaded
  and p.drupal_id = $1
  and d.storage_type = $2
union
select distinct
	r.order_no,
	r.data_subset,
	r.subset_name
from p_dataset d, p_dataset_role a, r_person p, r_data_subset r, x_context x
where d.dataset_id = a.dataset_id
  and a.user_id = p.person_id
  and d.storage_type in (
	select object_key from x_context
	where context_type = 'action'
	  and context_key = 'pool_data'
	  and object_type = 'storage_type'
  )
  and d.storage_type = r.storage_type
  and p.drupal_id = $1
  and d.storage_type = $2
  and x.context_type = 'property'
  and x.context_key = 'static_variables'
  and x.object_type = 'storage_type'
  and x.object_key = d.storage_type
order by order_no
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$arr=array();

if ($res = $db->query($sql, array($uid,$storage_type))) {
	$i=0;
	foreach ($res as $row)
		$arr[$i++]=$row;
}
//header("Content-Type: application/json; charset=UTF-8");
echo '{"data_subset":'.json_encode($arr).'}';
?>
