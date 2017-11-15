/*
	Creator: Mats J Svensson, CAnMove
*/

create table x_table
(
	name				varchar(50) not null unique,
	version				varchar(5),
	remark				text
);
insert into x_table (name) select distinct 'x_table' from x_table
where not exists (select 1 from x_table where name='x_table');
update x_table set version='1.0' where name='x_table';
