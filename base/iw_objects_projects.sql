/*
	Creator: Mats J Svensson, CAnMove
*/

create or replace view iw_objects_projects (
	dataprovider,
	dataowner,
	projectid,
	projectlsid
) as
select
	cast ('CAnMove' as varchar),
	cast (s_dataset_owner(dataset_id) as varchar),
	cast (title as varchar),
	cast (null as varchar)
from p_dataset
where storage_type in ('GEN')
  and public
order by 1,2,3
;
insert into x_table (name) select distinct 'iw_objects_projects' from x_table
where not exists (select 1 from x_table where name='iw_objects_projects');
update x_table set version='1.0' where name='iw_objects_projects';

grant select on iw_objects_projects to canmove_ws;
