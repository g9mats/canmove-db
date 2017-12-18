<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$storage_type=$_POST['storage_type'];
if ($storage_type=="") {
	echo "<p>You must specify a storage type.</p>";
	return;
}
$data_subset=$_POST['data_subset'];
if ($data_subset=="") {
	echo "<p>You must specify a data subset.</p>";
	return;
}
$help_text=$_POST['help_text'];
if ($help_text=="") {
	echo "<p>You must specify a help text choice.</p>";
	return;
}
$var_arr=$_POST['var_arr'];
if (count($var_arr)==0) {
	echo "<p>You must specify at least one variable.</p>";
	return;
}

require "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// SQL: select variables
$sql_var="
select
	order_no,
	header,
	nullable,
	case data_type
		when 'float' then 'decimal'
		else data_type
	end data_type,
	unit,
	remark
from r_data
where storage_type = $1
  and data_subset = $2
  and header in ('".implode("','",$var_arr)."')
union
select
	d.order_no,
	a.header,
	true,
	case d.data_type
		when 'float' then 'decimal'
		else d.data_type
	end data_type,
	d.unit,
	a.remark
from r_data d, r_data_alias a
where d.data_id = a.data_id
  and d.storage_type = $1
  and d.data_subset = $2
  and a.header in ('".implode("','",$var_arr)."')
order by order_no, header
";
require $DBRoot."/lib/XMLDocument.php";

$xmldoc = new XMLDocument(strtolower($storage_type)."_".$data_subset."protocol.xml", $storage_type." ".$data_subset);

// Build header row in XML document
$xmldoc->add_row();
$xmldoc->add_cell('Protocol', 'String');
$xmldoc->add_cell('', 'String');
if ($help_text=="Y") {
	$xmldoc->add_cell('', 'String');
	$xmldoc->add_cell('', 'String');
}
$xmldoc->add_cell('Variables marked with * must have a value', 'String');
$xmldoc->add_row();
$xmldoc->add_row();
$xmldoc->add_cell('Variables', 'String');
$xmldoc->add_cell('Values', 'String');
if ($help_text=="Y") {
	$xmldoc->add_cell('Data Type', 'String');
	$xmldoc->add_cell('Unit', 'String');
	$xmldoc->add_cell('Help', 'String');
}
$xmldoc->add_row();
$res=$db->query($sql_var,array($storage_type,$data_subset));
foreach ($res as $row) {
	$xmldoc->add_row();
	if ($row['nullable']=="f")
		$xmldoc->add_cell ($row['header']." *", 'String');
	else
		$xmldoc->add_cell ($row['header'], 'String');
	if ($help_text=="Y") {
		$xmldoc->add_cell ('', 'String');
		$xmldoc->add_cell ($row['data_type'], 'String');
		$xmldoc->add_cell ($row['unit'], 'String');
		$xmldoc->add_cell ($row['remark'], 'String');
	}
}

// display and quit
$xmldoc->display();
?>
