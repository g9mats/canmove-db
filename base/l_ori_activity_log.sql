/*
	Creator: Mats J Svensson, CAnMove
*/

create table l_ori_activity_log
(
	activity_id			serial primary key,
	dataset_id			integer not null,
	animal				varchar not null,
	capture_time		varchar not null,
	experiment_no		integer not null,
	phase_no			integer not null,
	version				integer not null default 1,
	order_no			integer not null,
	time				varchar,
	data_id				integer not null references r_data (data_id),
	data_value			varchar
);
insert into x_table (name) select distinct 'l_ori_activity_log' from x_table
where not exists (select 1 from x_table where name='l_ori_activity_log');
update x_table set version='1.0' where name='l_ori_activity_log';

create index l_ori_activity_log_dataset_id_key
on l_ori_activity_log (
	dataset_id,
	animal,
	capture_time,
	experiment_no,
	phase_no,
	version,
	order_no,
	time
);
