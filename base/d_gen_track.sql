/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_gen_track
(
	track_id			serial primary key,
	animal_id			integer not null references d_gen_animal (animal_id),
	start_capture_id	integer not null references d_gen_capture (capture_id),
	end_capture_id		integer references d_gen_capture (capture_id),
	start_time			timestamp with time zone,
	end_time			timestamp with time zone,
	remark				varchar(500)
);
insert into x_table (name) select distinct 'd_gen_track' from x_table
where not exists (select 1 from x_table where name='d_gen_track');
update x_table set version='1.0' where name='d_gen_track';

create index d_gen_track_animal_id_key
on d_gen_track (
	animal_id
);
create index d_gen_track_start_capture_id_key
on d_gen_track (
	start_capture_id
);
create index d_gen_track_start_time_key
on d_gen_track (
	start_time
);
create index d_gen_track_end_time_key
on d_gen_track (
	end_time
);
