<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
?>

<p>
Enter part of dataset title or owner to search for or leave blank.
</p>

<!-- Form for entering seach string -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<table><tr>
	<td>Search text:</td>
	<td><input name="find" /></td>
	</tr><tr>
	<td>Sort order:</td>
	<td><select name="order" required="required">
		<option value="title,owner" selected>Title,Owner</option>
		<option value="owner,title">Owner,Title</option>
	</select></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">List</button></td>
	</tr></table>
</form>
