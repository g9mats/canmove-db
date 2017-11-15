/*
	Creator: Mats J Svensson, CAnMove
*/

create table l_database
(
	database_id			serial primary key,
	data_type			varchar(4) not null references r_data_type (data_type),
	database_name		varchar(50) not null
);
insert into x_table (name) select distinct 'l_database' from x_table
where not exists (select 1 from x_table where name='l_database');
update x_table set version='1.0' where name='l_database';

create index l_database_data_type_key
on l_database (
	data_type
);
create index l_database_database_name_key
on l_database (
	database_name
);
