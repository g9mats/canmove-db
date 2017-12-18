<?php
// Creator: Mats J Svensson, CAnMove

// Define session wide variables
session_start();
$_SESSION['CMProdHost']="ldb010.srv.lu.se"; // CAnMove production hostname
$_SESSION['CMDevHost']="canmove-dev.biol.lu.se"; // CAnMove development hostname
if ($_SERVER['SERVER_NAME']==$_SESSION['CMProdHost']) {
	$_SESSION['CMServer']=$_SESSION['CMProdHost']; // CAnMove webserver
	$_SESSION['CMDatabase']="canmove"; // CAnMove database
	}
else {
	$_SESSION['CMServer']=$_SESSION['CMDevHost']; // CAnMove webserver
	$_SESSION['CMDatabase']="canmove"; // CAnMove database
	}
$_SESSION['CMPortal']="http://".$_SESSION['CMServer']."/db/portal.php"; // CAnMove web portal
$_SESSION['Database']=$_SESSION['CMDatabase']; // Default database
$_SESSION['Username']="canmove"; // Default username

// Define background colours for table rows
$_SESSION['EVENROW']="#ccecff";
$_SESSION['ODDROW']="#ffffff";

?>
