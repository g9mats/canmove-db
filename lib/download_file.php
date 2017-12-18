<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$aname=$_GET['aname'];
if ($aname=="") {
	echo "<p>You must specify an archive file name.</p>";
	return;
}
$oname=$_GET['oname'];
if ($oname=="") {
	echo "<p>You must specify an original file name.</p>";
	return;
}
/*
echo $aname."<br>";
echo $oname."<br>";
*/
if (ob_get_level()) {
	ob_end_clean();
}
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$oname);
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: '.filesize($aname));
readfile($aname);
?>
