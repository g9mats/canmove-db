/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_ori_phase
(
	phase_id			serial primary key,
	experiment_id		integer not null references d_ori_experiment (experiment_id),
	phase_no			integer not null,
	start_time			timestamp,
	end_time			timestamp,
	middle_time			timestamp,
	remark				varchar(500)
);
insert into x_table (name) select distinct 'd_ori_phase' from x_table
where not exists (select 1 from x_table where name='d_ori_phase');
update x_table set version='1.0' where name='d_ori_phase';

create index d_ori_phase_experiment_id_key
on d_ori_phase (
	experiment_id
);
create index d_ori_phase_start_time_key
on d_ori_phase (
	start_time
);
create index d_ori_phase_end_time_key
on d_ori_phase (
	end_time
);
create index d_ori_phase_middle_time_key
on d_ori_phase (
	middle_time
);
