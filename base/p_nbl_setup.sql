/*
	Creator: Mats J Svensson, CAnMove
*/

create table p_nbl_setup
(
	setup_id			serial primary key,
	dataset_id			integer not null references p_dataset (dataset_id),
	setup				varchar(50) not null,
	remark				varchar(500),
unique (dataset_id, setup)
);
insert into x_table (name) select distinct 'p_nbl_setup' from x_table
where not exists (select 1 from x_table where name='p_nbl_setup');
update x_table set version='1.0' where name='p_nbl_setup';
