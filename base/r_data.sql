/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_data
(
	data_id				serial primary key,
	storage_type		varchar(4) not null,
	data_subset			varchar(15) not null,
	header				varchar(50) not null,
	order_no			integer,
	table_name			varchar(50) not null,
	column_name			varchar(50) not null,
	column_type			varchar(3) not null,
	data_type			varchar(8) not null,
	case_type			varchar(5) not null,
	mandatory			boolean not null,
	load_name			varchar(50) not null,
	nullable			boolean not null,
	unit				varchar(25),
	remark				varchar(500),
unique (storage_type,data_subset,header)
);
insert into x_table (name) select distinct 'r_data' from x_table
where not exists (select 1 from x_table where name='r_data');
update x_table set version='1.0' where name='r_data';

create index r_data_order_no_key
on r_data (
	storage_type,
	data_subset,
	order_no
);
create index r_data_table_name_key
on r_data (
	table_name
);
create index r_data_column_name_key
on r_data (
	column_name
);
