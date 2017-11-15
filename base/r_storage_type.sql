/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_storage_type
(
	storage_type_id		serial primary key,
	storage_type		varchar(4) not null unique,
	storage_name		varchar(50) not null,
	remark				text
);
insert into x_table (name) select distinct 'r_storage_type' from x_table
where not exists (select 1 from x_table where name='r_storage_type');
update x_table set version='1.0' where name='r_storage_type';
