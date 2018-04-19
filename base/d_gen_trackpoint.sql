/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_gen_trackpoint
(
	trackpoint_id		serial primary key,
	device_id			integer not null references d_gen_device (device_id),
	period				varchar not null default '-',
	version				integer not null default 1,
	log_time			timestamp with time zone not null,
	quality				varchar(3),
	latitude			float,
	longitude			float,
	speed				float,
	course				float,
	altitude			float
);
insert into x_table (name) select distinct 'd_gen_trackpoint' from x_table
where not exists (select 1 from x_table where name='d_gen_trackpoint');
update x_table set version='1.0' where name='d_gen_trackpoint';

create index d_gen_trackpoint_device_id_key
on d_gen_trackpoint (
	device_id,
	period,
	version,
	log_time
);
