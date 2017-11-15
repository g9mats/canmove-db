/*
	Creator: Mats J Svensson, CAnMove
*/

create table p_column
(
	column_id			serial primary key,
	dataset_id			integer not null references p_dataset (dataset_id),
	data_subset			varchar(15) not null,
	varset				varchar(10) not null default '-',
	order_no			integer not null,
	data_id				integer not null references r_data (data_id),
	load_name			varchar(50) not null,
	header				varchar(50) not null
);
insert into x_table (name) select distinct 'p_column' from x_table
where not exists (select 1 from x_table where name='p_column');
update x_table set version='1.0' where name='p_column';

create index p_column_order_no_key
on p_column (
	dataset_id,
	data_subset,
	varset,
	order_no
);
