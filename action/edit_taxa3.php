<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

$sql="
select
	taxon_id,
	itis_tsn,
	taxon,
	remark
from p_taxon
where dataset_id = $1
order by taxon
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
Edit taxa for a dataset. hej
</p>

<!-- Form for selection of dataset -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<button type="submit">Continue</button>
</form>

<?php $db->disconnect(); ?>
