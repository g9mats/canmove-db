/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_ori_estimation
(
	estimation_id		serial primary key,
	phase_id			integer not null references d_ori_phase (phase_id),
	version				integer not null default 1,
	operator_id			integer not null references r_person (person_id),
	activity			integer,
	concentration		integer,
	direction			float,
	modality			integer,
	act_plus_conc		integer,
	remark				varchar(500)
);
insert into x_table (name) select distinct 'd_ori_estimation' from x_table
where not exists (select 1 from x_table where name='d_ori_estimation');
update x_table set version='1.0' where name='d_ori_estimation';

create index d_ori_estimation_phase_id_key
on d_ori_estimation (
	phase_id
);
