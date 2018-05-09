<?php
// Creator: Mats J Svensson, CAnMove

// Connect to database
$DB=pg_connect("host=localhost user=".$Username." dbname=".$Database);
if (isset($user->uid) && ($user->uid != "")) {
	$_DBsql = "select time_zone from r_person where drupal_id = $1";
	$_DBres = pg_query_params($DB, $_DBsql, array($user->uid))
		or die ($_DBsql."\n".pg_last_error()."\n");
	$_DBrow = pg_fetch_assoc($_DBres);
	$_DBsql = "set time zone '".$_DBrow['time_zone']."'";
	$_DBres = pg_query($DB, $_DBsql)
		or die ($_DBsql."\n".pg_last_error()."\n");
	pg_free_result($_DBres);
}
?>
