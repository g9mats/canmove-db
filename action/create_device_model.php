<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
</p>

<!-- Form for insert of device model -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<table><tr>
	<td>Model:</td>
	<td><input name="model" required="required" size="30" value="" /></td>
	</tr><tr>
	<td>Manufacturer:</td>
	<td><input name="manufacturer" size="30" value="" /></td>
	</tr><tr>
	<td>Weight:</td>
	<td><input name="weight" size="8" value="" /></td>
	</tr><tr>
	<td>Description:</td>
	<td><input name="description" size="50" value="" /></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Save</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
