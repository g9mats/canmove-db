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
	initcap(r.subset_name) as subset_name,
	initcap(f.data_status) as data_status,
	f.original_name,
	to_char(f.upload_time,'YYYY-MM-DD HH24:MI') as upload_time,
	case when registered then 'R' else '-' end ||
	case when imported then 'I' else '-' end ||
	case when validated then 'V' else '-' end ||
	case when loaded then 'L' else '-' end as flags
from p_dataset d, l_file f, r_data_subset r
where d.dataset_id = f.dataset_id
  and d.storage_type = r.storage_type
  and f.data_subset = r.data_subset
  and d.dataset_id = $1
  and not f.deleted
order by r.order_no, f.data_status, f.original_name
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
</p>

<table>
<tr>
<th>Subset</th>
<th>Status</th>
<th>Name</th>
<th>Uploaded</th>
<th>Flags</th>
</tr>
<?php
// For every file
if ($res = $db->query($sql,array($dataset_id)))
	foreach ($res as $row) {
?>
<tr>
<td><?php echo $row['subset_name']; ?></td>
<td><?php echo $row['data_status']; ?></td>
<td><?php echo $row['original_name']; ?></td>
<td><?php echo $row['upload_time']; ?></td>
<td style="font-family:courier">[<?php echo $row['flags']; ?>]</td>
</tr>
<?php
	}
?>
</table>
<p>
Flags: R=Registered, I=Imported, V=Validated, L=Loaded
</p>

<?php $db->disconnect(); ?>
