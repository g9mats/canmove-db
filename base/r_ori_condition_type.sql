/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_ori_condition_type
(
	condition_type_id	serial primary key,
	condition_type		varchar(50) not null unique,
	remark				text
);
insert into x_table (name) select distinct 'r_ori_condition_type' from x_table
where not exists (select 1 from x_table where name='r_ori_condition_type');
update x_table set version='1.0' where name='r_ori_condition_type';

create index r_ori_condition_type_condition_type_key
on r_ori_condition_type (
	condition_type
);
