/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_ori_sensor_type
(
	sensor_type_id		serial primary key,
	sensor_type			varchar(50) not null unique,
	remark				text
);
insert into x_table (name) select distinct 'r_ori_sensor_type' from x_table
where not exists (select 1 from x_table where name='r_ori_sensor_type');
update x_table set version='1.0' where name='r_ori_sensor_type';
