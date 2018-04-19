/*
	Creator: Mats J Svensson, CAnMove
*/

create table l_load
(
	load_id				serial primary key,
	data_type			varchar(4) not null references r_data_type (data_type),
	database_name		varchar(50),
	dataset_id			integer,
	loaded				boolean not null default false,
	create_time			timestamp with time zone not null,
	remark				text,
unique (data_type,database_name,dataset_id)
);
insert into x_table (name) select distinct 'l_load' from x_table
where not exists (select 1 from x_table where name='l_load');
update x_table set version='1.0' where name='l_load';

create index l_load_create_time_key
on l_load (
	create_time
);
