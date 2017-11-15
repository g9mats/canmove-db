/*
	Creator: Mats J Svensson, CAnMove
*/

create table p_nbl_condition
(
	condition_id		serial primary key,
	context_id			integer not null references p_nbl_context (context_id),
	condition_type		varchar(50) not null,
	condition_value		varchar(50) not null,
unique (context_id, condition_type, condition_value)
);
insert into x_table (name) select distinct 'p_nbl_condition_value' from x_table
where not exists (select 1 from x_table where name='p_nbl_condition_value');
update x_table set version='1.0' where name='p_nbl_condition_value';
