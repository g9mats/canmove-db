<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);

require "../lib/settings.inc";
require_once $DBRoot."/lib/DBLink.php";

function export_menu ($parent) {

	$sql_menu="
select
	m.link_title,
	m.mlid,
	m.depth,
	m.has_children,
	n.nid,
	u.alias
from
	drupal_menu_links m,
	drupal_node n,
	drupal_url_alias u
where m.link_title = n.title
 and 'node/'||n.nid = u.source
 and m.menu_name='menu-action-menu'
 and m.plid = $1
order by m.weight
	";

	$sql_role_id="
select
	rid
from
	drupal_role
order by rid
	";

	$sql_grant="
select
	gid,
	grant_view,
	grant_update,
	grant_delete
from
	drupal_node_access
where nid = $1
  and gid = $2
order by gid
	";

	require "../lib/settings.inc";

	$db = new DBLink("localhost", $DrDatabase, "postgres");
	$db->connect();
	$mres = $db->query($sql_menu, array($parent));
	foreach ($mres as $mrow) {
		echo $mrow['depth'];
		echo "\t";
		echo $mrow['link_title'];
		echo "\t";
		echo $mrow['alias'];
		$rres = $db->query($sql_role_id, array());
		foreach ($rres as $rrow) {
			echo "\t";
			$gres = $db->query($sql_grant, array($mrow['nid'],$rrow['rid']));
			foreach ($gres as $grow) {
				$auth="";
				if ($grow['grant_view'])
					$auth.=":view";
				if ($grow['grant_update'])
					$auth.=":update";
				if ($grow['grant_delete'])
					$auth.=":delete";
				if ($auth != "")
					$auth=substr($auth,1);
				echo $auth;
			}
		}
		echo "\n";
		if ($mrow['has_children'] == 1)
			export_menu($mrow['mlid']);
	}
}

$sql_role_name="
select
	name
from
	drupal_role
order by rid
	";

$db = new DBLink("localhost", $DrDatabase, "postgres");
$db->connect();

echo "Level\tTitle\tAlias";
$rres = $db->query($sql_role_name, array());
foreach ($rres as $rrow) {
	echo "\t".$rrow['name'];
}
echo "\n";

export_menu(0);

?>
