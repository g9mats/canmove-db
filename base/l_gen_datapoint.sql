/*
	Creator: Mats J Svensson, CAnMove
*/

create table l_gen_datapoint
(
	datapoint_id		serial primary key,
	dataset_id			integer not null,
	device_id			integer not null,
	period				varchar not null default '-',
	version				integer not null default 1,
	varset				varchar not null default '-',
	order_no			integer not null,
	log_time			varchar,
	data_id				integer not null references r_data (data_id),
	data_value			varchar
);
insert into x_table (name) select distinct 'l_gen_datapoint' from x_table
where not exists (select 1 from x_table where name='l_gen_datapoint');
update x_table set version='1.0' where name='l_gen_datapoint';

create index l_gen_datapoint_dataset_id_key
on l_gen_datapoint (
	dataset_id,
	device_id,
	period,
	version,
	varset,
	order_no,
	log_time
);
