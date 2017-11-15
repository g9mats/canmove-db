/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_data_subset
(
	data_subset_id		serial primary key,
	storage_type		varchar(4) not null, 
	data_subset			varchar(15) not null, 
	subset_name			varchar(15) not null,
	order_no			integer not null,
	versions			boolean not null,
	register			boolean not null,
unique (storage_type, data_subset)
);
insert into x_table (name) select distinct 'r_data_subset' from x_table
where not exists (select 1 from x_table where name='r_data_subset');
update x_table set version='1.0' where name='r_data_subset';
