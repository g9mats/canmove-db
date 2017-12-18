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
require_once $DBRoot."/lib/DBLink.php";
require $DBRoot."/lib/get_storage_type.php";

// SQL: select file record
$sql="
select
	dataset_id,
	data_status,
	original_name,
	archive_name
from l_file
where file_id = $1
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$fileinfo=array();
$count=0;
foreach ($file_arr as $file_id)
	if ($res = $db->query($sql,array($file_id))) {
		$row=$res[0];
		$oname=$row['original_name'];
		$dataset_id=$row['dataset_id'];
		$data_status=$row['data_status'];
		$path=$DataRoot."/".$storage_type."/".$dataset_id."/".$data_status;
		$aname=$path."/".$row['archive_name'];
		$fileinfo[$count]['aname']=$aname;
		$fileinfo[$count]['oname']=$oname;
		$count++;
	}
?>
<div id=status></div>
<script>
(function(){
	var fileinfo;
	var url = "<?php echo $WebRoot; ?>/db/lib/download_file.php";
	var iFrame = [];
	
	if (typeof JSON === 'object' && typeof JSON.parse === 'function')
		fileinfo=JSON.parse('<?php echo json_encode($fileinfo); ?>');
	else
		fileinfo=eval("(+"<?php echo json_encode($fileinfo); ?>+")");
	for (var i=0; i<fileinfo.length; i++) {
		/*
		alert("aname="+fileinfo[i].aname);
		alert("oname="+fileinfo[i].oname);
		*/
		iFrame.splice(i,0,document.createElement('iframe'));
		/*
		iFrame[i].src = url+
			"?aname="+encodeURIComponent(fileinfo[i].aname)+
			"&oname="+encodeURIComponent(fileinfo[i].oname);
		*/
		iFrame[i].src = url+
			"?aname="+fileinfo[i].aname+
			"&oname="+fileinfo[i].oname;
		iFrame[i].setAttribute("style","visibility:hidden;display:none;");
		document.body.appendChild(iFrame[i]);
	}
	if (i==1)
		document.getElementById("status").innerHTML=i+" file downloaded.";
	else
		document.getElementById("status").innerHTML=i+" files downloaded.";
})()
</script>

<!--
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="" type="hidden" />
	<input name="dataset_id" value="<?php echo $dataset_id; ?>" type="hidden" />
	<button type="submit">Download more files</button>
</form>
-->

<?php $db->disconnect(); ?>
