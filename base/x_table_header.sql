/*
	Creator: Mats J Svensson, CAnMove
*/

create table x_table_header
(
	table_id			serial primary key,
	table_name			varchar(50) not null unique,
	table_header		varchar(50) not null,
	order_no			integer
);
insert into x_table (name) select distinct 'x_table_header' from x_table
where not exists (select 1 from x_table where name='x_table_header');
update x_table set version='1.0' where name='x_table_header';

create index x_table_header_table_header_key
on x_table_header (
	table_header
);
