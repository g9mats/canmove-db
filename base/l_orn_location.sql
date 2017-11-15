/*
	Creator: Mats J Svensson, CAnMove
*/

create table l_orn_location
(
	file_id				integer not null unique,
	dataset_id			integer not null,
	location			varchar(50) not null,
	latitude			varchar,
	longitude			varchar,
	altitude			varchar,
	declination			varchar
);
insert into x_table (name) select distinct 'l_orn_location' from x_table
where not exists (select 1 from x_table where name='l_orn_location');
update x_table set version='1.0' where name='l_orn_location';

create index l_orn_location_dataset_id_key
on l_orn_location (
	dataset_id
);
