/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_trr_trackpoint
(
	trackpoint_id		serial primary key,
	track_id			integer not null references d_trr_track (track_id),
	azimuth				float not null,
	elevation			float not null,
	distance			float not null,
	dist_locked			boolean not null,
	azi_ele_locked		boolean not null,
	tick_time			timestamp with time zone not null
);
insert into x_table (name) select distinct 'd_trr_trackpoint' from x_table
where not exists (select 1 from x_table where name='d_trr_trackpoint');
update x_table set version='1.0' where name='d_trr_trackpoint';

create index d_trr_trackpoint_track_id_key
on d_trr_trackpoint (
	track_id
);
