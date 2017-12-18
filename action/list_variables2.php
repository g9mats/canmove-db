<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
$data_subset=$_POST['data_subset'];
if ($data_subset=="") {
	echo "<p>You must specify a data subset.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// Build SQL select statement for variables
$sql="
select
	p.varset,
	p.order_no,
	p.header,
	h.table_header,
	case r.mandatory
		when true then 'Mandatory'
		else 'Optional'
	end mandatory,
	case r.data_type
		when 'float' then 'decimal'
		else r.data_type
	end data_type,
	r.remark,
	r.unit
from p_column p, r_data r, x_table_header h
where p.data_id = r.data_id
  and r.table_name = h.table_name
  and p.dataset_id=$1
  and p.data_subset=$2
order by p.varset, p.order_no
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
</p>

<table border="1">
<tr>
<?php
	if ($data_subset == 'datapoint')
		echo "<th>VarSet</th>";
?>
<th>No</th>
<th>Header</th>
<th>Object</th>
<th>Mandatory</th>
<th>Data Type</th>
<th>Unit</th>
<th>Remark</th>
</tr>
<?php
// Get variable information and present it in a table
if ($res = $db->query($sql,array($dataset_id,$data_subset)))
	foreach ($res as $row) {
?>
<tr>
<?php
	if ($data_subset == 'datapoint')
		echo "<td>".$row['varset']."</td>";
?>
<td><?php echo $row['order_no']; ?></td>
<td><?php echo $row['header']; ?></td>
<td><?php echo $row['table_header']; ?></td>
<td><?php echo $row['mandatory']; ?></td>
<td><?php echo $row['data_type']; ?></td>
<td><?php echo $row['unit']; ?></td>
<td><?php echo $row['remark']; ?></td>
</tr>
<?php
	}
?>
</table>

<?php $db->disconnect(); ?>
