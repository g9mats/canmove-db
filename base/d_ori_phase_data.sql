/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_ori_phase_data
(
	phase_data_id		serial primary key,
	phase_id			integer not null references d_ori_phase (phase_id),
	order_no			integer not null,
	data_id				integer not null references r_data (data_id),
	data_value			varchar(100)
);
insert into x_table (name) select distinct 'd_ori_phase_data' from x_table
where not exists (select 1 from x_table where name='d_ori_phase_data');
update x_table set version='1.0' where name='d_ori_phase_data';

create index d_ori_phase_data_order_no_key
on d_ori_phase_data (
	experiment_id,
	order_no
);
create index d_ori_phase_data_data_id_key
on d_ori_phase_data (
	data_id
);
