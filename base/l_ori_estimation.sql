/*
	Creator: Mats J Svensson, CAnMove
*/

create table l_ori_estimation
(
	estimation_id		serial primary key,
	dataset_id			integer not null,
	animal				varchar not null,
	capture_time		varchar not null,
	experiment_no		integer not null,
	phase_no			integer not null,
	version				integer not null default 1,
	operator_id			varchar,
	activity			varchar,
	concentration		varchar,
	direction			varchar,
	modality			varchar,
	act_plus_conc		varchar,
	estimation_remark	varchar
);
insert into x_table (name) select distinct 'l_ori_estimation' from x_table
where not exists (select 1 from x_table where name='l_ori_estimation');
update x_table set version='1.0' where name='l_ori_estimation';

create index l_ori_estimation_dataset_id_key
on l_ori_estimation (
	dataset_id,
	animal,
	capture_time,
	experiment_no,
	phase_no
);
