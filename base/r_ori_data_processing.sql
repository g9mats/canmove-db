/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_ori_data_processing
(
	data_processing_id	serial primary key,
	data_processing		varchar(50) not null unique,
	remark				text
);
insert into x_table (name) select distinct 'r_ori_data_processing' from x_table
where not exists (select 1 from x_table where name='r_ori_data_processing');
update x_table set version='1.0' where name='r_ori_data_processing';
