/*
	Creator: Mats J Svensson, CAnMove
*/

create table l_orn_track
(
	file_id				integer not null,
	track_no			integer not null,
	dataset_id			integer not null,
	start_time			timestamp with time zone not null,
	itis_tsn			integer,
	taxon				varchar not null,
	species_no			integer not null,
	english_name		varchar,
	swedish_name		varchar,
	wind_direction		varchar,
	wind_speed			varchar,
	barometer			varchar,
	temperature			varchar,
	quantity			varchar,
	sex					varchar,
	age					varchar,
	crop				varchar,
	flight_style		varchar,
	activity			varchar,
	remark				varchar,
unique (file_id, track_no)
);
insert into x_table (name) select distinct 'l_orn_track' from x_table
where not exists (select 1 from x_table where name='l_orn_track');
update x_table set version='1.0' where name='l_orn_track';

create index l_orn_track_dataset_id_key
on l_orn_track (
	dataset_id
);
