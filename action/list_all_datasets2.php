<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$find=$_POST['find'];
$order=$_POST['order'];
if ($order=="") {
	echo "<p>You must specify a sort order.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: select datasets
$sql="
select
	d.dataset_id,
	d.title,
	t.data_name,
	s_dataset_owner(d.dataset_id,'<br/>') as owner,
	d.start_date,
	s_dataset_taxon(d.dataset_id,'<br/>') as taxon
from p_dataset d, r_data_type t
where d.data_type = t.data_type
  and upper(d.title||' '||s_dataset_owner(d.dataset_id)) like upper('%'||$1||'%')
order by ORDER
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
List of all
<?php
$i=0;
echo "datasets";
if ($find!="")
	echo ' matching search text: "'.$find.'"';
?>
. Click on titles for dataset details.
</p>

<table>
<tr>
<th>Title</th>
<th>Data Type</th>
<th>Owner</th>
<th>Start Date</th>
<th>Taxa</th>
</tr>

<?php
// Change sort order clause
$sql = str_replace("ORDER",$order,$sql);
// For every dataset
if ($res = $db->query($sql,array($find)))
	foreach ($res as $row) {
$i++;
?>
<tr>
<td>
	<a onclick="get_details(<?php echo $row['dataset_id']; ?>)" style="cursor:pointer">
	<?php echo $row['title']; ?></a>
</td>
<td><?php echo $row['data_name'] ?></td>
<td><?php echo $row['owner']; ?></td>
<td><?php echo $row['start_date']; ?></td>
<td><?php echo $row['taxon']; ?></td>
</tr>

<?php
	}
?>
</table>
<?php
echo $i." dataset";
if ($i != 1) echo "s";
?>

<?php $db->disconnect(); ?>

<form id="detail_form" action="show_dataset_information" method="post">
	<input name="next_step" value="2" type="hidden" />
	<input name="dataset_id" id="dataset_id" value="" type="hidden" />
</form>

<script>
function get_details(did) {
var form = document.forms.detail_form;
  document.getElementById("dataset_id").value=did;
  form.submit();
}
</script>
