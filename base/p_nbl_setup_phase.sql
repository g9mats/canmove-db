/*
	Creator: Mats J Svensson, CAnMove
*/

create table p_nbl_setup_phase
(
	setup_phase_id		serial primary key,
	setup_id			integer not null references p_nbl_setup (setup_id),
	start_time			float not null,
	end_time			float not null,
	context_id			integer not null references p_nbl_context (context_id),
	remark				varchar(500),
unique (setup_id, start_time)
);
insert into x_table (name) select distinct 'p_nbl_setup_phase' from x_table
where not exists (select 1 from x_table where name='p_nbl_setup_phase');
update x_table set version='1.0' where name='p_nbl_setup_phase';

create index p_nbl_setup_phase_context_id_key
on p_nbl_setup_phase (
	context_id
);
