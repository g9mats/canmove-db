/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_nbl_trackpoint
(
	trackpoint_id		serial primary key,
	track_id			integer not null references d_nbl_track (track_id),
	file_id				integer not null references d_nbl_file (file_id),
	frame				integer not null,
	time				float not null,
	x					float,
	y					float,
	z					float
);
insert into x_table (name) select distinct 'd_nbl_trackpoint' from x_table
where not exists (select 1 from x_table where name='d_nbl_trackpoint');
update x_table set version='1.0' where name='d_nbl_trackpoint';

create unique index d_nbl_trackpoint_time_key
on d_nbl_trackpoint (
	track_id,
	time
);
