/*
	Creator: Mats J Svensson, CAnMove
*/

create or replace view iw_w2_animaltrack (
	dataprovider,
	dataowner,
	projectid,
	datasetdoi,
	species,
	specieslsid,
	animalid,
	animallsid,
	tagid,
	sensorid,
	observationdate,
	latitude,
	longitude,
	height,
	depth,
	srid,
	spatialpoint,
	nontransformeddata,
	confidencevalue
) as
select
	cast ('CAnMove' as varchar),
	cast (s_dataset_owner(ds.dataset_id) as varchar),
	cast (ds.title as varchar),
	cast (null as varchar),
	a.taxon,
	cast (null as varchar),
	cast (a.animal as varchar),
	cast (null as varchar),
	d.device,
	d.device,
	cast (p.log_time at time zone 'UTC' as timestamp with time zone),
	cast (p.latitude as float8),
	cast (p.longitude as float8),
	cast (p.altitude as float8),
	cast (null as float8),
	4326,
	'POINT('||p.longitude||' '||p.latitude||' '||p.altitude||')',
	cast (null as boolean),
	cast (null as integer)
from p_dataset ds,
		d_gen_animal a, d_gen_track t, d_gen_device d, d_gen_trackpoint p
where ds.dataset_id = a.dataset_id
  and a.animal_id = t.animal_id
  and t.track_id = d.track_id
  and d.device_id = p.device_id
  and ds.storage_type in ('GEN')
  and ds.public
order by 1,2,3,7,9,10,11
;
insert into x_table (name) select distinct 'iw_w2_animaltrack' from x_table
where not exists (select 1 from x_table where name='iw_w2_animaltrack');
update x_table set version='1.0' where name='iw_w2_animaltrack';

grant select on iw_w2_animaltrack to canmove_ws;
