/*
	Creator: Mats J Svensson, CAnMove
*/

create or replace view iw_objects_supportedcollections (
	dataprovider,
	dataowner,
	wqlcollectionname
) as
select
	cast ('CAnMove' as varchar),
	cast ('Lund University' as varchar),
	ext_object
from iw_table
where active
order by 1,2,3
;
insert into x_table (name) select distinct 'iw_objects_supportedcollections' from x_table
where not exists (select 1 from x_table where name='iw_objects_supportedcollections');
update x_table set version='1.0' where name='iw_objects_supportedcollections';

grant select on iw_objects_supportedcollections to canmove_ws;
