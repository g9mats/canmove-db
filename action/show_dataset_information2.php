<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
require_once "canmove.inc";
require_once $DBRoot."/lib/DBLink.php";

// SQL: select dataset and project
$sql="
select
	p.title pn,
	d.title dn,
	s_dataset_owner($1) as owner,
	t.data_name data_name,
	d.method,
	d.site,
	d.start_date,
	d.end_date,
	d.animal_num,
	d.track_num,
	d.animal_db,
	d.track_db,
	d.data_location,
	c.first_name||' '||c.last_name as contact,
	u.name as drupal_name,
	u.mail as drupal_mail,
	d.remark
from p_dataset d
	left outer join r_person c on d.contact_id = c.person_id
	left outer join drupal_users u on c.drupal_id = u.uid,
	p_project p, r_data_type t
where d.project_id = p.project_id
  and d.data_type = t.data_type
  and d.dataset_id = $1
";

// SQL: select taxa
$sql_taxa="
select
	taxon,
	case remark is null
		when true then ''
		else remark
	end as remark
from p_taxon
where dataset_id = $1
order by taxon
";

// Get dataset information and present it in a table
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
if ($res = $db->query($sql, array($dataset_id))) {
	$row = $res[0];
?>

<style type="text/css">
.prompt {text-align:left;font-weight:bold;width:120px}
</style>

<p>
	<b>Dataset information</b>
	<button style="position:relative;left:200px" onclick="window.history.back()">Return</button>
</p>

<table>
<tr><td class="prompt">Project:</td><td><?php echo $row['pn']; ?></td></tr>
<tr><td class="prompt">Dataset:</td><td><?php echo $row['dn']; ?></td></tr>
<tr><td class="prompt">Owner:</td><td><?php echo $row['owner']; ?></td></tr>
</table><p></p><table>
<tr><td class="prompt">Data Type:</td><td><?php echo $row['data_name']; ?></td></tr>
<tr><td class="prompt">Method:</td><td><?php echo $row['method']; ?></td></tr>
<tr><td class="prompt">Site:</td><td><?php echo $row['site']; ?></td></tr>
</table><p></p><table>
<tr>
	<td class="prompt">Start Date:</td><td style="width:120px"><?php echo $row['start_date']; ?></td>
	<td class="prompt">End Date:</td><td><?php echo $row['end_date']; ?></td>
</tr>
<tr>
	<td class="prompt">No of Animals:</td><td><?php echo $row['animal_num']; ?></td>
	<td class="prompt">- in Database:</td><td><?php echo $row['animal_db']; ?></td>
</tr>
<tr>
	<td class="prompt">No of Tracks:</td><td><?php echo $row['track_num']; ?></td>
	<td class="prompt">- in Database:</td><td><?php echo $row['track_db']; ?></td>
</tr>
</table><p></p><table>
<tr><td class="prompt">Data Location:</td><td><?php echo $row['data_location']; ?></td></tr>
<tr>
	<td class="prompt">Contact Person:</td>
	<td>
<?php
	if ($row['drupal_name'] != "") {
		echo '<a target="_blank" href="';
		echo "http://www.lunduniversity.lu.se/lucat/user/";
		echo $row['drupal_name'];
		echo '">';
	} elseif ($row['drupal_mail'] != "") {
		echo '<a href="mailto:';
		echo $row['drupal_mail'];
		echo '">';
	}
	echo $row['contact']."</a>";
?>
	</td>
</tr>
</table><p></p><table>
<tr><td class="prompt">Remark:</td><td><?php echo $row['remark']; ?></td></tr>
</table>
<?php
}
?>

<p></p>

<table>
<tr>
<th style="text-align:left">Taxon</th>
<th style="text-align:left">Remark</th>
</tr>
<?php
if ($res = $db->query($sql_taxa, array($dataset_id)))
	foreach ($res as $row) {
?>
<tr>
<td><?php echo $row['taxon']; ?></td>
<td><?php echo $row['remark']; ?></td>
</tr>
<?php
}
?>
</table>

<?php
$db->disconnect();
?>
