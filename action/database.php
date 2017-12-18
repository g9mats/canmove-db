<p>
This is the
<?php
if ($CMSite == "Production")
	//echo "<b><span style='background-color:#fbe5f0'>CAnMove Production Database</span></b>";
	echo "<b><span style='background-color:#dce9ee'>CAnMove Production Database</span></b>";
else
	//echo "<b><span style='background-color:#dfefe8'>CAnMove Development Database</span></b>";
	echo "<b><span style='background-color:#d6e4dc'>CAnMove Development Database</span></b>";
?>
 for data owners and curators.
<?php
if ($user->uid == "") {
?>
<link rel="stylesheet" type="text/css" href="../lib/help.css">
<p>
You can use the Public Information menu on the left hand side anonymously
to view CAnMove meta data but you must log in to create datasets and
manage actual movement data.
</p>
<p>
For information on accounts go to
<a href="<?php echo $DBHelp ?>?topic=canmove_account">CAnMove account</a>.
</p>
<?php
}
?>
</p>
<p>
Other sites:<br/>
<ul>
<li><b>
<?php
if ($CMSite == "Production")
	echo "<a style='background-color:#d6e4dc' href='http://".$CMDevHost."/canmove/db/action/database'>CAnMove Development Database</a>";
else
	echo "<a style='background-color:#dce9ee' href='http://".$CMProdHost."/canmove/db/action/database'>CAnMove Production Database</a>";
?>
.</b></li>
<li><b><a style='background-color:#f5f2ec' href="http://canmove.lu.se/">CAnMove Web</a></b>.</li>
</ul>
</p>
<p>
Contact: <b><a target="_blank" href="http://www.lunduniversity.lu.se/lucat/user/biol-msn">Mats Svensson</a></b>
</p>
