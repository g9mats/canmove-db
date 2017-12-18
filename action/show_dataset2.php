<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

// SQL: select dataset and project
$sql_dataset="
select
	p.title pn,
	d.title dn,
	s_dataset_owner($1) as owner,
	t.data_name data_name,
	s.storage_name storage_name,
	d.method,
	d.site,
	d.start_date,
	d.end_date,
	d.animal_num,
	d.track_num,
	d.animal_db,
	d.track_db,
	d.data_location,
	c.first_name||' '||c.last_name as name,
	case d.public
		when true then 'Yes'
		else 'No'
	end public,
	d.release_date,
	d.remark
from p_dataset d left outer join r_person c
	on d.contact_id = c.person_id, p_project p, r_data_type t, r_storage_type s
where d.project_id = p.project_id
  and d.data_type = t.data_type
  and d.storage_type = s.storage_type
  and dataset_id = $1
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

// SQL: select dataset role
$sql_role="
select
	p.first_name||' '||p.last_name as name,
	case a.user_role
		when 'O' then 'Owner'
		when 'A' then 'Admin'
		when 'W' then 'Write'
		when 'R' then 'Read'
		else a.user_role
	end user_role
from p_dataset_role a, r_person p
where a.user_id = p.person_id
  and dataset_id = $1
order by p.last_name, p.first_name
";

// Get dataset information and present it in a table
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
if ($res = $db->query($sql_dataset, array($dataset_id))) {
	$row = $res[0];
?>

<style type="text/css">
.prompt {font-weight:bold;width:95px}
</style>

<p><b>Dataset information</b></p>

<table>
<tr><td class="prompt">Project:</td><td colspan="3"><?php echo $row['pn']; ?></td></tr>
<tr><td class="prompt">Dataset Id:</td><td><?php echo $dataset_id; ?></td></tr>
<tr><td class="prompt">Title:</td><td colspan="3"><?php echo $row['dn']; ?></td></tr>
<tr><td class="prompt">Owner:</td><td><?php echo $row['owner']; ?></td></tr>
<tr><td><br/></td></tr>
<tr><td class="prompt">Data Type:</td><td><?php echo $row['data_name']; ?></td></tr>
<tr><td class="prompt">Storage Type:</td><td><?php echo $row['storage_name']; ?></td></tr>
<tr><td class="prompt">Method:</td><td colspan="3"><?php echo $row['method']; ?></td></tr>
<tr><td class="prompt">Site:</td><td colspan="3"><?php echo $row['site']; ?></td></tr>
<tr><td><br/></td></tr>
<tr>
	<td class="prompt">Start Date:</td><td><?php echo $row['start_date']; ?></td>
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
<tr><td><br/></td></tr>
<tr><td class="prompt">Data Location:</td><td colspan="3"><?php echo $row['data_location']; ?></td></tr>
<tr><td class="prompt">Contact Person:</td><td><?php echo $row['name']; ?></td></tr>
<tr>
	<td class="prompt">Public:</td><td><?php echo $row['public']; ?></td>
	<td class="prompt">Release Date:</td><td><?php echo $row['release_date']; ?></td>
</tr>
<tr><td><br/></td></tr>
<tr><td class="prompt">Remark:</td><td colspan="3"><?php echo $row['remark']; ?></td></tr>
</table>
<?php
}
?>

<p></p>

<table>
<tr>
<th>Taxon</th>
<th>Remark</th>
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

<p></p>

<table>
<tr>
<th>User</th>
<th>Role</th>
</tr>
<?php
if ($res = $db->query($sql_role, array($dataset_id)))
	foreach ($res as $row) {
?>
<tr>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['user_role']; ?></td>
</tr>
<?php
}
?>
</table>

<?php
$db->disconnect();
?>
