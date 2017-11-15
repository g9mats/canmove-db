/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_ori_assessment_data
(
	assessment_data_id	serial primary key,
	assessment_id		integer not null references d_ori_assessment (assessment_id),
	order_no			integer not null,
	data_id				integer not null references r_data (data_id),
	data_value			varchar(100)
);
insert into x_table (name) select distinct 'd_ori_assessment_data' from x_table
where not exists (select 1 from x_table where name='d_ori_assessment_data');
update x_table set version='1.0' where name='d_ori_assessment_data';

create index d_ori_assessment_data_order_no_key
on d_ori_assessment_data (
	assessment_id,
	order_no
);
create index d_ori_assessment_data_data_id_key
on d_ori_assessment_data (
	data_id
);
