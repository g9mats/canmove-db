<?php
// Creator: Mats J Svensson, CAnMove

// Connect to database
$_SESSION['DB']=pg_connect("host=localhost user=canmove dbname=".$_SESSION['Database']);

?>
