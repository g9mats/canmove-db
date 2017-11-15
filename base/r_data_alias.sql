/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_data_alias
(
	data_alias_id		serial primary key,
	data_id				integer not null references r_data (data_id),
	header				varchar(50) not null,
	remark				varchar(500),
	keep_alias			boolean not null default false
);
insert into x_table (name) select distinct 'r_data_alias' from x_table
where not exists (select 1 from x_table where name='r_data_alias');
update x_table set version='1.0' where name='r_data_alias';

create index r_data_alias_header_key
on r_data_alias (
	header
);
