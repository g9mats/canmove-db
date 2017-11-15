/*
	Creator: Mats J Svensson, CAnMove
*/

create or replace view iw_objects_animals (
	dataprovider,
	dataowner,
	projectid,
	projectlsid,
	animalid,
	animallsid
) as
select
	cast ('CAnMove' as varchar),
	cast (s_dataset_owner(ds.dataset_id) as varchar),
	cast (ds.title as varchar),
	cast (null as varchar),
	cast (a.animal as varchar),
	cast (null as varchar)
from p_dataset ds, d_gen_animal a
where ds.dataset_id = a.dataset_id
  and ds.storage_type in ('GEN')
  and ds.public
order by 1,2,3,5
;
insert into x_table (name) select distinct 'iw_objects_animals' from x_table
where not exists (select 1 from x_table where name='iw_objects_animals');
update x_table set version='1.0' where name='iw_objects_animals';

grant select on iw_objects_animals to canmove_ws;
