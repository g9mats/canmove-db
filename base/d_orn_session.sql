/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_orn_session
(
	session_id			serial primary key,
	dataset_id			integer not null references p_dataset (dataset_id),
	location			varchar(50) not null,
	session_time		timestamp not null,
	file_id				integer not null unique,
	latitude			float not null,
	longitude			float not null,
	altitude			float not null,
	declination			float not null,
	height_datum		float,
	height_source		varchar(20),
	wind_source			varchar(20),
	anemometer_height	float,
	taxa_file			varchar(50),
	activity_file		varchar(50),
	remark				text,
unique (location, session_time)
);
insert into x_table (name) select distinct 'd_orn_session' from x_table
where not exists (select 1 from x_table where name='d_orn_session');
update x_table set version='1.0' where name='d_orn_session';

create index d_orn_session_dataset_id_key
on d_orn_session (
	dataset_id
);
