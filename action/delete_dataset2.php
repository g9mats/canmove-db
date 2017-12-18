<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}

?>

<p></p> 

<!-- Form for confirmation of deletion of dataset -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="3" type="hidden"/>
	<input name="dataset_id" value="<?php echo $dataset_id ?>" type="hidden" />
	<table><tr>
	<td>Are you sure?</td>
	<td>
		<input type="radio" name="answer" value="N" checked="checked" onchange="set_text()"/>No
		<input type="radio" id="answer" name="answer" value="Y" onchange="set_text()" />Yes
	</td>
	</tr><tr>
	<td></td>
	<td><button id="goon" type="submit">Quit</button></td>
	</tr></table>
</form>

<script>
function set_text() {
	if (document.getElementById("answer").checked == true) {
		document.getElementById("goon").innerHTML = "Commit";
	} else {
		document.getElementById("goon").innerHTML = "Quit";
	}
}
</script>
