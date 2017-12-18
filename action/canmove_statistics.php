<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
require_once $DBRoot."/lib/DBLink.php";

// SQL: select statistics per data type
$sql="
select
	r.data_name as data_type,
	sum(d.animal_db) as animals,
	sum(d.track_db) as tracks
from r_data_type r, p_dataset d
where r.data_type = d.data_type
group by r.data_name
order by r.data_name
";
$sqlx="
select
	r.data_name as data_type,
	count(distinct a.animal_id) as animals,
	count(distinct t.track_id) as tracks
from d_gen_animal a left outer join d_gen_track t on
		a.animal_id = t.animal_id,
	p_dataset d, r_data_type r
where a.dataset_id = d.dataset_id
  and d.data_type = r.data_type
  and d.storage_type = 'GEN'
group by 1
union
select
	r.data_name as data_type,
	count(distinct d.track_id) as animals,
	count(distinct d.track_id) as tracks
from d_nbl_track d, r_data_type r
where r.storage_type = 'NBL'
group by 1
union
select
	r.data_name as data_type,
	count(distinct a.animal_id) as animals,
	count(distinct t.experiment_id) as tracks
from d_ori_animal a left outer join d_ori_experiment t on
		a.animal_id = t.animal_id,
	r_data_type r
where r.storage_type = 'ORI'
group by 1
union
select
	r.data_name as data_type,
	count(distinct d.track_id) as animals,
	count(distinct d.track_id) as tracks
from d_orn_track d, r_data_type r
where r.storage_type = 'ORN'
  and d.species_no <> -1
group by 1
union
select
	r.data_name as data_type,
	count(distinct d.track_id) as animals,
	count(distinct d.track_id) as tracks
from d_trr_bird d, r_data_type r
where r.storage_type = 'TRR'
group by 1
order by 1
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();
?>

<p>
CAnMove Statistics.
</p>

<table>
<tr>
<th>Data Type</th>
<th style="text-align:center">Animals</th>
<th style="text-align:center">Tracks/Experiments</th>
</tr>

<?php
if ($res = $db->query($sql))
	$numa=0; $numt=0;
	foreach ($res as $row) {
?>
<tr>
<td><?php echo $row['data_type']; ?></td>
<td style="text-align:center"><?php echo $row['animals']; ?></td>
<td style="text-align:center"><?php echo $row['tracks']; ?></td>
</tr>
<?php
$numa+=$row['animals'];
$numt+=$row['tracks'];
	}
?>
<tr>
<td>----------------</td>
<td style="text-align:center">--------</td>
<td style="text-align:center">--------</td>
</tr>
<tr>
<td>Total</td>
<td style="text-align:center"><?php echo $numa; ?></td>
<td style="text-align:center"><?php echo $numt; ?></td>
</tr>
</table>

<?php $db->disconnect(); ?>
