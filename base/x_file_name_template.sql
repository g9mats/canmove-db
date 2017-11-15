/*
	Creator: Mats J Svensson, CAnMove
*/

create table x_file_name_template
(
	x_template_id		serial primary key,
	storage_type		varchar(4) not null,
	data_subset			varchar(15) not null,
	template_text		varchar(30) not null,
	order_no			integer,
unique (storage_type, data_subset, template_text)
);
insert into x_table (name) select distinct 'x_file_name_template' from x_table
where not exists (select 1 from x_table where name='x_file_name_template');
update x_table set version='1.0' where name='x_file_name_template';
