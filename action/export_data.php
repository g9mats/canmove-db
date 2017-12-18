<?php
// Creator: Mats J Svensson, CAnMove

require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
echo "<script>";
require_once $DBRoot."/lib/ctx_dataset_subset.js";
require_once $DBRoot."/lib/ctx_dataset_subset_version.js";
require_once $DBRoot."/lib/ctx_dataset_version.js";
require_once $DBRoot."/lib/ctx_dataset_varset.js";
echo "</script>";
$ajax_file=$WebRoot."/db/action/export_data_subset.php";
$ajax_file2=$WebRoot."/db/action/export_data_subset_version.php";
$ajax_file3=$WebRoot."/db/action/export_data_version.php";
$ajax_file4=$WebRoot."/db/action/export_data_varset.php";

// SQL: select datasets
$sql="
select
	d.dataset_id,
	d.title
from p_dataset d, p_dataset_role a, r_person p
where d.dataset_id = a.dataset_id
  and a.user_id = p.person_id
  and d.storage_type in (
	select object_key from x_context
	where context_type = 'action'
	  and context_key = 'export_data'
	  and object_type = 'storage_type'
  )
  and p.drupal_id = $1
order by d.title
";
?>

<p>
Export of a dataset into an XML file (can be read by e.g. Excel).
</p>
<p>
You must select dataset before you can select data subset. You will only find those datasets that you have access to and with a data type that is supported by this export utility.
</p>

<!-- Form for selection of dataset -->
<form id="dsform" action="/db/action/export_data3.php" method="post">
	<input id="next_step" name="next_step" value="3" type="hidden" />
	<table><tr>
	<td>Dataset:</td>
	<td><select id="dataset_id" name="dataset_id" required="required"
			onchange="ctx_dataset_subset('<?php echo $ajax_file; ?>');choose()">
		<option value="" selected>Select dataset</option>
<?php
	if ($res = $db->query($sql, array($user->uid))) {
		foreach ($res as $row)
			echo "<option value='".$row['dataset_id']."'>".
				$row['title']."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td>Data Subset:</td>
	<td><select id="data_subset" name="data_subset" required="required" onchange="choose()">
	</select></td>
	</tr><tr>
	<td id="vlabel">Version:</td>
	<td><select id="version" name="version" required="required">
		<option value="1">1</option>
	</select></td>
	</tr><tr>
	<td id="vslabel">Variable Set:</td>
	<td><select id="varset" name="varset" required="required">
		<option value="-">-</option>
	</select></td>
	</tr><tr>
	<td></td>
	<td id="please">Please be patient while data is exported.</td>
	</tr><tr>
	<td></td>
	<td><button id="goon" type="submit">Export</button></td>
	</tr></table>
</form>

<script>
(function(){
	document.getElementById("vlabel").style.visibility = "hidden";
	document.getElementById("version").style.visibility = "hidden";
	document.getElementById("vslabel").style.visibility = "hidden";
	document.getElementById("varset").style.visibility = "hidden";
})()
</script>
<script>
function choose() {
	if (document.getElementById("data_subset").value == "datapoint") {
		document.getElementById("dsform").action = "<?php echo $DrAction ?>";
		document.getElementById("next_step").value = "2";
		document.getElementById("please").style.visibility = "hidden";
		document.getElementById("goon").innerHTML = "Next";
	} else {
		document.getElementById("dsform").action = "/db/action/export_data3.php";
		document.getElementById("next_step").value = "3";
		document.getElementById("please").style.visibility = "visible";
		document.getElementById("goon").innerHTML = "Export";
	}
	var ds=":datapoint:trackpoint:count:estimation:";
	/*if (ds.includes(document.getElementById("data_subset").value)) {*/
	if (ctx_dataset_subset_version('<?php echo $ajax_file2; ?>') == "t") {
		document.getElementById("vlabel").style.visibility = "visible";
		document.getElementById("version").style.visibility = "visible";
		ctx_dataset_version('<?php echo $ajax_file3; ?>');
	} else {
		document.getElementById("vlabel").style.visibility = "hidden";
		document.getElementById("version").style.visibility = "hidden";
	}
	if (document.getElementById("data_subset").value == "datapoint") {
		document.getElementById("vslabel").style.visibility = "visible";
		document.getElementById("varset").style.visibility = "visible";
		ctx_dataset_varset('<?php echo $ajax_file4; ?>');
	} else {
		document.getElementById("vslabel").style.visibility = "hidden";
		document.getElementById("varset").style.visibility = "hidden";
	}
}
</script>

<?php $db->disconnect(); ?>
