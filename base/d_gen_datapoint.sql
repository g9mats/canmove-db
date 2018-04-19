/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_gen_datapoint
(
	datapoint_id		serial primary key,
	device_id			integer not null references d_gen_device (device_id),
	period				varchar not null default '-',
	version				integer not null default 1,
	varset				varchar not null default '-',
	order_no			integer not null,
	log_time			timestamp with time zone not null,
	data_id				integer not null references r_data (data_id),
	data_value			varchar(100)
);
insert into x_table (name) select distinct 'd_gen_datapoint' from x_table
where not exists (select 1 from x_table where name='d_gen_datapoint');
update x_table set version='1.0' where name='d_gen_datapoint';

create index d_gen_datapoint_period_key
on d_gen_datapoint (
	device_id,
	period,
	version,
	varset,
	order_no,
	log_time
);

create index d_gen_datapoint_version_key
on d_gen_datapoint (
	device_id,
	version,
	order_no,
	log_time
);
