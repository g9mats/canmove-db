<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$storage_type=$_POST['storage_type'];
if ($storage_type=="") {
	echo "<p>You must specify a storage type.</p>";
	return;
}
require "./canmove.inc";
require $DBRoot."/lib/XMLDocument.php";

// Log on to database using common routine
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

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
	d.unit,
	d.remark
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
	d.unit,
	a.remark
from r_data d, r_data_subset s, r_data_alias a, x_table_header h
where d.storage_type = s.storage_type
  and d.data_subset = s.data_subset
  and d.data_id = a.data_id
  and d.table_name = h.table_name
  and d.storage_type = $1
order by s_order_no, subset_name, h_order_no, mandatory, header
";

$xmldoc = new XMLDocument("all_".strtolower($storage_type)."_variables.xml",
			"All ".$storage_type." Variables");
$xmlrec = array();

// Build header row in XML document
$xmldoc->add_row();
$xmldoc->add_cell ('Data Subset', 'String');
$xmldoc->add_cell ('Object', 'String');
$xmldoc->add_cell ('Mandatory', 'String');
$xmldoc->add_cell ('Header', 'String');
$xmldoc->add_cell ('Data Type', 'String');
$xmldoc->add_cell ('Unit', 'String');
$xmldoc->add_cell ('Remark', 'String');

// Get variable information and present it in a table
if ($res = $db->query($sql_var,array($storage_type))) {
	foreach ($res as $row) {
		$xmldoc->add_row();
		$xmldoc->add_cell ($row['subset_name'], 'String');
		$xmldoc->add_cell ($row['table_header'], 'String');
		$xmldoc->add_cell ($row['mandatory'], 'String');
		$xmldoc->add_cell ($row['header'], 'String');
		$xmldoc->add_cell ($row['data_type'], 'String');
		$xmldoc->add_cell ($row['unit'], 'String');
		$xmldoc->add_cell ($row['remark'], 'String');
	}
}

// display and quit
$xmldoc->display();
?>
