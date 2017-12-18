<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$storage_type=$_POST['storage_type'];
if ($storage_type=="") {
	echo "<p>You must specify a storage type.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: select storage type name
$sql_name="
select
	storage_name
from r_storage_type
where storage_type = $1
";

// Build SQL select statements for animal, track and capture records
$sql_var="
select
	s.order_no as s_order_no,
	s.subset_name,
	h.table_header,
	h.order_no as h_order_no,
	case d.mandatory
		when true then 'Mandatory'
		else 'Optional'
	end mandatory,
	d.header,
	case d.data_type
		when 'float' then 'decimal'
		else d.data_type
	end data_type,
	d.remark,
	d.unit
from r_data d, r_data_subset s, x_table_header h
where d.storage_type = s.storage_type
  and d.data_subset = s.data_subset
  and d.table_name = h.table_name
  and d.storage_type = $1
union
select
	s.order_no as s_order_no,
	s.subset_name,
	h.table_header,
	h.order_no as h_order_no,
	case d.mandatory
		when true then 'Mandatory'
		else 'Optional'
	end mandatory,
	a.header,
	case d.data_type
		when 'float' then 'decimal'
		else data_type
	end data_type,
	a.remark,
	d.unit
from r_data d, r_data_subset s, r_data_alias a, x_table_header h
where d.storage_type = s.storage_type
  and d.data_subset = s.data_subset
  and d.data_id = a.data_id
  and d.table_name = h.table_name
  and d.storage_type = $1
order by s_order_no, subset_name, h_order_no, mandatory, header
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

if ($res=$db->query($sql_name, array($storage_type)))
	$storage_name=$res[0]['storage_name'];
?>

<h3><?php echo $storage_name; ?> Storage Variables</h3>
<table border="1">
<tr>
<th>Data Subset</th>
<th>Object</th>
<th>Mandatory</th>
<th>Header</th>
<th>Data Type</th>
<th>Unit</th>
<th>Remark</th>
</tr>
<?php
// Get variable information and present it in a table
if ($res = $db->query($sql_var,array($storage_type)))
	foreach ($res as $row) {
?>
<tr>
<td><?php echo $row['subset_name']; ?></td>
<td><?php echo $row['table_header']; ?></td>
<td><?php echo $row['mandatory']; ?></td>
<td><?php echo $row['header']; ?></td>
<td><?php echo $row['data_type']; ?></td>
<td><?php echo $row['unit']; ?></td>
<td><?php echo $row['remark']; ?></td>
</tr>
<?php
	}
?>
</table>

<?php $db->disconnect(); ?>
