<?php
// Creator: Mats J Svensson, CAnMove

require "./canmove.inc";
$node_path = drupal_lookup_path("alias","node/".$node->nid);
$DrAction=$DrRoot."/".$node_path;
if (isset($_POST['next_step']))
	$next_step=$_POST['next_step'];
else
	$next_step="";
require $CMRoot."/".$node_path.$next_step.".php";
?>
