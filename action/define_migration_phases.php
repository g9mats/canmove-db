<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";
$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

$sql="
select
	d.dataset_id,
	d.title
from p_dataset d, p_dataset_role a, r_person p
where d.dataset_id = a.dataset_id
  and a.user_id = p.person_id
  and d.storage_type in (
	select object_key from x_context
	where context_type = 'action'
	  and context_key = 'define_migration_phases'
	  and object_type = 'storage_type'
  )
  and a.user_role in ('O','A','W')
  and p.drupal_id = $1
  and exists (
	select p.device_id, p.log_time, p.version
	from d_gen_animal x, d_gen_track y, d_gen_device z, d_gen_trackpoint p
	where x.dataset_id = d.dataset_id
	  and x.animal_id = y.animal_id
	  and y.track_id = z.track_id
	  and z.device_id = p.device_id
	except
	select p.device_id, p.log_time, p.version
	from d_gen_animal x, d_gen_track y, d_gen_device z, d_gen_trackpoint p, d_gen_migration_phase m
	where x.dataset_id = a.dataset_id
	  and x.animal_id = y.animal_id
	  and y.track_id = z.track_id
	  and z.device_id = p.device_id
	  and p.device_id = m.device_id
	  and p.version = m.version
	  and p.log_time between m.start_log_time and m.end_log_time
	)
order by d.title
";
?>

<p>
Define migration phases and connect trackpoints to them.
</p>
<p>
You will only find those datasets that you have write access to and with a data type that is supported by this utility.
</p>

<!-- Form for selection of storage type -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="2" type="hidden" />
	<table><tr>
	<td>Dataset:</td>
	<td><select id="dataset_id" name="dataset_id" required="required">
		<option value="" selected>Select dataset</option>
<?php
	if ($res = $db->query($sql, array($user->uid))) {
		foreach ($res as $row)
			echo "<option value='".$row['dataset_id']."'>".
				$row['title']."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Next</button></td>
	</tr></table>
</form>

<?php $db->disconnect(); ?>
