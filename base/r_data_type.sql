/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_data_type
(
	data_type_id		serial primary key,
	data_type			varchar(4) not null unique,
	data_name			varchar(50) not null,
	storage_type		varchar(4) not null references r_storage_type (storage_type),
	remark				text
);
insert into x_table (name) select distinct 'r_data_type' from x_table
where not exists (select 1 from x_table where name='r_data_type');
update x_table set version='1.0' where name='r_data_type';
