/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_ori_capture_data
(
	capture_data_id		serial primary key,
	capture_id			integer not null references d_ori_capture (capture_id),
	order_no			integer not null,
	data_id				integer not null references r_data (data_id),
	data_value			varchar(100)
);
insert into x_table (name) select distinct 'd_ori_capture_data' from x_table
where not exists (select 1 from x_table where name='d_ori_capture_data');
update x_table set version='1.0' where name='d_ori_capture_data';

create index d_ori_capture_data_order_no_key
on d_ori_capture_data (
	capture_id,
	order_no
);
create index d_ori_capture_data_data_id_key
on d_ori_capture_data (
	data_id
);
