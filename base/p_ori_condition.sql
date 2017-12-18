/*
	Creator: Mats J Svensson, CAnMove
*/

create table p_ori_condition
(
	condition_id		serial primary key,
	context_id			integer not null references p_ori_context (context_id),
	condition_type		varchar(50) not null,
	condition_value		varchar(50) not null,
unique (context_id, condition_type, condition_value)
);
insert into x_table (name) select distinct 'p_ori_condition' from x_table
where not exists (select 1 from x_table where name='p_ori_condition');
update x_table set version='1.0' where name='p_ori_condition';
