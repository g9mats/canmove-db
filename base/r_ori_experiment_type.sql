/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_ori_experiment_type
(
	experiment_type_id	serial primary key,
	experiment_type		varchar(50) not null unique,
	remark				text
);
insert into x_table (name) select distinct 'r_ori_experiment_type' from x_table
where not exists (select 1 from x_table where name='r_ori_experiment_type');
update x_table set version='1.0' where name='r_ori_experiment_type';
