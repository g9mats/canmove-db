/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_ori_condition_value
(
	condition_value_id	serial primary key,
	condition_type_id	integer not null references r_ori_condition_type (condition_type_id),
	condition_value		varchar(50) not null unique,
	remark				text,
unique (condition_type_id, condition_value)
);
insert into x_table (name) select distinct 'r_ori_condition_value' from x_table
where not exists (select 1 from x_table where name='r_ori_condition_value');
update x_table set version='1.0' where name='r_ori_condition_value';
