/*
	Creator: Mats J Svensson, CAnMove
*/

create table p_nbl_context
(
	context_id			serial primary key,
	dataset_id			integer not null references p_dataset (dataset_id),
	context				integer not null,
unique (dataset_id, context)
);
insert into x_table (name) select distinct 'p_nbl_context' from x_table
where not exists (select 1 from x_table where name='p_nbl_context');
update x_table set version='1.0' where name='p_nbl_context';
