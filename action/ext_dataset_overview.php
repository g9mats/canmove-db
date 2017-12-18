<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once "./canmove.inc";
require_once $DBRoot."/lib/DBLink.php";

// SQL: select datasets
$sql="
select
	d.dataset_id,
	t.data_name,
	s_dataset_owner(d.dataset_id,'<br/>') as owner,
	d.title,
	d.start_date,
	s_dataset_taxon(d.dataset_id,'<br/>') as taxon
from p_dataset d, r_data_type t
where d.data_type = t.data_type
order by t.data_name, owner, d.title
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>
<style>
a.bbblue {color:#0000bb;text-decoration:none}
a:link {color:blue;text-decoration:none}
a:visited {color:blue;text-decoration:none}
a:hover {color:blue;text-decoration:underline}
a:active {color:red;text-decoration:underline}
</style>

<p>
List of CAnMove datasets.
</p>

<table border="0" style="text-align:left">
<tr>
<th>Data Type</th>
<th>Owner</th>
<th>Title</th>
<th>Start Date</th>
<th>Taxa</th>
</tr>

<?php
$i=0;
// For every dataset
if ($res = $db->query($sql))
	foreach ($res as $row) {
	//echo "<tr style='background-color:#ccecff'>"; //ljusblå
	//echo "<tr style='background-color:#b9d3dc'>"; //stålblå
	//echo "<tr style='background-color:#dce9ee'>"; //blekblå
	//echo "<tr style='background-color:#f4e2e3'>"; //rosa
	//echo "<tr style='background-color:#d6e4dc'>"; //grön
	//echo "<tr style='background-color:#f5f2ec'>"; //beige
	//echo "<tr style='background-color:#dfdcd7'>"; //grå
if ($i % 2 == 0)
	echo "<tr style='background-color:#dce9ee'>"; //blekblå
else
	echo "<tr>";
$i++;
?>
<td><?php echo $row['data_name'] ?></td>
<td><?php echo $row['owner']; ?></td>
<td>
	<a class="bbblue" onclick="get_details(<?php echo $row['dataset_id']; ?>)" style="cursor:pointer">
	<?php echo $row['title']; ?></a>
</td>
<td><?php echo $row['start_date']; ?></td>
<td><?php echo $row['taxon']; ?></td>
</tr>

<?php
	}
?>
</table>
<br/>
<?php echo $i." datasets as of ".date("Y-m-d"); ?>

<?php $db->disconnect(); ?>

<form id="detail_form" action="show_dataset_information2.php" method="post">
	<input name="dataset_id" id="dataset_id" value="" type="hidden" />
</form>

<script>
function get_details(did) {
var form = document.forms.detail_form;
  document.getElementById("dataset_id").value=did;
  form.submit();
}
</script>
