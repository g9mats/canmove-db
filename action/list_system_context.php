<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";

// SQL: select context type
$sql_ctx_type="
select distinct
	context_type
from x_context
order by context_type
";

// SQL: select context key
$sql_ctx_key="
select distinct
	context_key
from x_context
order by context_key
";

// SQL: select object type
$sql_obj_type="
select distinct
	object_type
from x_context
order by object_type
";

// SQL: select object key
$sql_obj_key="
select distinct
	object_key
from x_context
order by object_key
";

// SQL: select object key 2
$sql_obj_key2="
select distinct
	object_key2
from x_context
where object_key2 is not null
order by object_key2
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
</p>

<!-- Form for insert of context binding -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<table><tr>
	<td>Context Type:</td>
	<td><input id="context_type" name="context_type" value="" /></td>
	<td><select id="ctx_type_list" name="ctx_type_list" onchange="copy_ctx_type()">
		<option value="" selected>Select context type</option>
<?php
		if ($res = $db->query($sql_ctx_type)) {
			foreach ($res as $row)
				echo "<option value='".$row['context_type']."'>".$row['context_type']."</option>";
		}
?>
	</select></td>
	</tr><tr>
	<td>Context Key:</td>
	<td><input id="context_key" name="context_key" value="" /></td>
	<td><select id="ctx_key_list" name="ctx_key_list" onchange="copy_ctx_key()">
		<option value="" selected>Select context key</option>
<?php
		if ($res = $db->query($sql_ctx_key)) {
			foreach ($res as $row)
				echo "<option value='".$row['context_key']."'>".$row['context_key']."</option>";
		}
?>
	</select></td>
	</tr><tr>
	<td>Object Type:</td>
	<td><input id="object_type" name="object_type" value="" /></td>
	<td><select id="obj_type_list" name="obj_type_list" onchange="copy_obj_type()">
		<option value="" selected>Select object type</option>
<?php
		if ($res = $db->query($sql_obj_type)) {
			foreach ($res as $row)
				echo "<option value='".$row['object_type']."'>".$row['object_type']."</option>";
		}
?>
	</select></td>
	</tr><tr>
	<td>Object Key:</td>
	<td><input id="object_key" name="object_key" value="" /></td>
	<td><select id="obj_key_list" name="obj_key_list" onchange="copy_obj_key()">
		<option value="" selected>Select object key</option>
<?php
		if ($res = $db->query($sql_obj_key)) {
			foreach ($res as $row)
				echo "<option value='".$row['object_key']."'>".$row['object_key']."</option>";
		}
?>
	</select></td>
	</tr><tr>
	<td>Object Key 2:</td>
	<td><input id="object_key2" name="object_key2" value="" /></td>
	<td><select id="obj_key_list2" name="obj_key_list2" onchange="copy_obj_key2()">
		<option value="" selected>Select object key 2</option>
<?php
		if ($res = $db->query($sql_obj_key2)) {
			foreach ($res as $row)
				echo "<option value='".$row['object_key2']."'>".$row['object_key2']."</option>";
		}
?>
	</select></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">List</button></td>
	</tr></table>
</form>

<script>
function copy_ctx_type() {
	document.getElementById("context_type").value =
		document.getElementById("ctx_type_list").value;
}
function copy_ctx_key() {
	document.getElementById("context_key").value =
		document.getElementById("ctx_key_list").value;
}
function copy_obj_type() {
	document.getElementById("object_type").value =
		document.getElementById("obj_type_list").value;
}
function copy_obj_key() {
	document.getElementById("object_key").value =
		document.getElementById("obj_key_list").value;
}
function copy_obj_key2() {
	document.getElementById("object_key2").value =
		document.getElementById("obj_key_list2").value;
}
</script>

<?php $db->disconnect(); ?>
