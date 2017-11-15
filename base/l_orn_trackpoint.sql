/*
	Creator: Mats J Svensson, CAnMove
*/

create table l_orn_trackpoint
(
	file_id				integer not null,
	track_no			integer not null,
	trackpoint_no		integer not null,
	dataset_id			integer not null,
	elapsed_time		varchar,
	x					varchar,
	y					varchar,
	z					varchar,
	air_density			varchar,
unique (file_id, track_no, trackpoint_no)
);
insert into x_table (name) select distinct 'l_orn_trackpoint' from x_table
where not exists (select 1 from x_table where name='l_orn_trackpoint');
update x_table set version='1.0' where name='l_orn_trackpoint';

create index l_orn_trackpoint_dataset_id_key
on l_orn_trackpoint (
	dataset_id
);
