<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: select file records
$sql="
select
	'Register File' as action, s.order_no, s.subset_name,
	f.registered, f.imported, f.validated, f.loaded, f.original_name
from p_dataset d, l_file f, r_data_subset s
where d.dataset_id = f.dataset_id
  and d.storage_type = s.storage_type
  and f.data_subset = s.data_subset
  and d.dataset_id = $1
  and f.data_status='final'
  and not f.registered
  and not f.deleted
  and s.register
union
select
	'Import File' as action, s.order_no, s.subset_name,
	f.registered, f.imported, f.validated, f.loaded, f.original_name
from p_dataset d, l_file f, r_data_subset s
where d.dataset_id = f.dataset_id
  and d.storage_type = s.storage_type
  and f.data_subset = s.data_subset
  and d.dataset_id = $1
  and f.data_status='final'
  and (not s.register or f.registered)
  and not f.imported
  and not f.deleted
union
select
	'Validate Data' as action, s.order_no, s.subset_name,
	f.registered, f.imported, f.validated, f.loaded, f.original_name
from p_dataset d, l_file f, r_data_subset s
where d.dataset_id = f.dataset_id
  and d.storage_type = s.storage_type
  and f.data_subset = s.data_subset
  and d.dataset_id = $1
  and f.data_status='final'
  and f.imported
  and not f.validated
union
select
	'Load Data' as action, s.order_no, s.subset_name,
	f.registered, f.imported, f.validated, f.loaded, f.original_name
from p_dataset d, l_file f, r_data_subset s
where d.dataset_id = f.dataset_id
  and d.storage_type = s.storage_type
  and f.data_subset = s.data_subset
  and d.dataset_id = $1
  and f.data_status='final'
  and f.validated
  and not f.loaded
order by order_no, registered, imported, validated, loaded
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
</p>

<table>
<tr>
<th>Action</th>
<th>Data Subset</th>
<th>File</th>
</tr>
<?php
// For every file
if ($res = $db->query($sql,array($dataset_id)))
	foreach ($res as $row) {
?>
<tr>
<td><?php echo $row['action']; ?></td>
<td><?php echo $row['subset_name']; ?></td>
<td><?php echo $row['original_name']; ?></td>
</tr>
<?php
	}
?>
</table>

<?php $db->disconnect(); ?>
