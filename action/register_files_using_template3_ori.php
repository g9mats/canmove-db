<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
$file_arr=$_POST['file_arr'];
if ($file_arr[0]=="") {
	echo "<p>You must specify at least one file.</p>";
	return;
}
$prefix=$_POST['prefix'];
$template_text=$_POST['template_text'];
if ($template_text=="") {
	echo "<p>You must specify a template.</p>";
	return;
}
$suffix=$_POST['suffix'];
require_once $DBRoot."/lib/DBLink.php";

// SQL: get file record
$sql_file="
select original_name
from l_file
where file_id = $1
";

// SQL: update file record
$sql_update="
update l_file set
	version = $2,
	registered = true,
	reg_time = date_trunc('second',localtimestamp)
where file_id = $1
";

$sql_log="
insert into l_file_log (
	file_id,log_action,log_time
) values ($1, 'R', date_trunc('second',localtimestamp))
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$template=explode("_",$template_text);
$count=0;
foreach ($file_arr as $file_id) {
	$res = $db->query($sql_file,array($file_id));
	$oname = $res[0]['original_name'];
	$string=strtoupper($oname);
	if ($prefix!="") {
		$r=strpos($string,strtoupper($prefix));
		if ($r===false || $r!=0) {
			echo "Could not find prefix ".$prefix.
				" in file name ".$oname."<br/>";
			return;
		} else
			$string=substr($string,strlen($prefix));
	}
	if ($suffix!="") {
		$r=strrpos($string,strtoupper($suffix));
		if ($r===false || $r!=strlen($string)-strlen($suffix)) {
			echo "Could not find suffix ".$suffix.
				" in file name ".$oname."<br/>";
		} else
			$string=substr($string,0,strlen($string)-strlen($suffix));
	}
	$key=explode("_",$string);
	if (count($key)!=count($template)) {
		echo "Wrong number of key elements in file name: ".$oname."<br/>";
		return;
	}
	for ($i=0; $i<count($template); $i++) {
		if ($template[$i]=="version")
			eval ("\$".$template[$i]."=".$key[$i].";");
		else
			eval ("\$".$template[$i]."='".$key[$i]."';");
	}
	if ($version=="") $version=1;
	if ($res = $db->execute($sql_update,array($file_id,$version))) {
		$count++;
		$res=$db->execute($sql_log, array($file_id));
	}
}
if ($count==1)
	echo "<p>1 file registered.</p>";
else
	echo "<p>".$count." files registered.</p>";
?>

<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="" type="hidden" />
	<input name="dataset_id" value="<?php echo $dataset_id; ?>" type="hidden" />
	<button type="submit">Register more files</button>
</form>

<?php $db->disconnect(); ?>
