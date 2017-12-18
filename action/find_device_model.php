<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
Enter part of model or manufacturer to search for or leave blank.
</p>

<!-- Form for entering seach string -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<table><tr>
	<td>Search text:</td>
	<td><input name="find" /></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Search</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
