/*
	Creator: Mats J Svensson, CAnMove
*/

create table l_column
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
insert into x_table (name) select distinct 'l_column' from x_table
where not exists (select 1 from x_table where name='l_column');
update x_table set version='1.0' where name='l_column';

create index l_column_order_no_key
on l_column (
	dataset_id,
	data_subset,
	varset,
	order_no
);
