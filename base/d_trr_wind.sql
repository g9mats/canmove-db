/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_trr_wind
(
	wind_id				serial primary key,
	track_id			integer not null unique references d_trr_track (track_id),
	time_to_lock		integer not null,
	ground_wind_dir		float not null,
	ground_wind_speed	float not null,
	create_time			timestamp with time zone not null
);
insert into x_table (name) select distinct 'd_trr_wind' from x_table
where not exists (select 1 from x_table where name='d_trr_wind');
update x_table set version='1.0' where name='d_trr_wind';
