/*
	Creator: Mats J Svensson, CAnMove
*/

create table p_ori_setup_phase
(
	setup_phase_id		serial primary key,
	setup_id			integer not null references p_ori_setup (setup_id),
	phase_no			integer not null,
	phase_length		varchar(5),
	context_id			integer not null references p_ori_context (context_id),
	remark				varchar(500),
unique (setup_id, phase_no)
);
insert into x_table (name) select distinct 'p_ori_setup_phase' from x_table
where not exists (select 1 from x_table where name='p_ori_setup_phase');
update x_table set version='1.0' where name='p_ori_setup_phase';

create index p_ori_setup_phase_context_id_key
on p_ori_setup_phase (
	context_id
);
