/*
	Creator: Mats J Svensson, CAnMove
*/

create table x_context
(
	x_context_id		serial primary key,
	context_type		varchar(30) not null,
	context_key			varchar(30) not null,
	object_type			varchar(30) not null,
	object_key			varchar(30) not null,
	object_key2			varchar(50) not null,
	order_no			integer,
unique (context_type, context_key, object_type, object_key, object_key2)
);
insert into x_table (name) select distinct 'x_context' from x_table
where not exists (select 1 from x_table where name='x_context');
update x_table set version='1.0' where name='x_context';
