/*
	Creator: Mats J Svensson, CAnMove
*/

create table iw_table
(
	table_id			serial primary key,
	ext_object			varchar(100) not null unique,
	int_object			varchar(100) not null unique,
	active				boolean not null default false,
	remark				text
);
insert into x_table (name) select distinct 'iw_table' from x_table
where not exists (select 1 from x_table where name='iw_table');
update x_table set version='1.0' where name='iw_table';

grant select on iw_table to canmove_ws;
