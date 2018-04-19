/*
	Creator: Mats J Svensson, CAnMove
*/

create table l_orn_session
(
	file_id				integer not null unique,
	dataset_id			integer not null,
	session_time		timestamp with time zone not null,
	height_datum		varchar,
	height_source		varchar,
	wind_source			varchar,
	anemometer_height	varchar,
	taxa_file			varchar,
	activity_file		varchar
);
insert into x_table (name) select distinct 'l_orn_session' from x_table
where not exists (select 1 from x_table where name='l_orn_session');
update x_table set version='1.0' where name='l_orn_session';

create index l_orn_session_dataset_id_key
on l_orn_session (
	dataset_id
);
