/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_gen_device
(
	device_id			serial primary key,
	track_id			integer not null references d_gen_track (track_id),
	parent_id			integer references d_gen_device (device_id),
	device				varchar(15) not null,
	device_model_id		integer references r_device_model (device_model_id),
	device_attachment	varchar(50),
	order_no			integer not null,
	start_time			timestamp,
	end_time			timestamp,
	remark				varchar(500)
);
insert into x_table (name) select distinct 'd_gen_device' from x_table
where not exists (select 1 from x_table where name='d_gen_device');
update x_table set version='1.0' where name='d_gen_device';

create index d_gen_device_track_id_key
on d_gen_device (
	track_id
);
create index d_gen_device_device_key
on d_gen_device (
	device
);
create index d_gen_device_start_time_key
on d_gen_device (
	start_time
);
create index d_gen_device_end_time_key
on d_gen_device (
	end_time
);
