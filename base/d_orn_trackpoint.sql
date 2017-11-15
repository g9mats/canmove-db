/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_orn_trackpoint
(
	trackpoint_id		serial primary key,
	track_id			integer not null references d_orn_track (track_id),
	trackpoint_no		integer not null,
	elapsed_time		float not null,
	x					float not null,
	y					float not null,
	z					float not null,
	air_density			float,
unique (track_id, trackpoint_no)
);
insert into x_table (name) select distinct 'd_orn_trackpoint' from x_table
where not exists (select 1 from x_table where name='d_orn_trackpoint');
update x_table set version='1.0' where name='d_orn_trackpoint';
