/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_ori_activity_log
(
	activity_id			serial primary key,
	phase_id			integer not null references d_ori_phase (phase_id),
	version				integer not null default 1,
	order_no			integer not null,
	time				float not null,
	data_id				integer not null references r_data (data_id),
	data_value			varchar(100)
);
insert into x_table (name) select distinct 'd_ori_activity_log' from x_table
where not exists (select 1 from x_table where name='d_ori_activity_log');
update x_table set version='1.0' where name='d_ori_activity_log';

create index d_ori_activity_log_time_key
on d_ori_activity_log (
	phase_id,
	version,
	time,
	order_no
);
