/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_gen_migration_phase
(
	phase_id			serial primary key,
	device_id			integer not null references d_gen_device (device_id),
	version				integer not null default 1,
	start_log_time		timestamp with time zone not null,
	end_log_time		timestamp with time zone not null,
	phase_type			varchar(2) not null,
	phase_index			integer not null
);
insert into x_table (name) select distinct 'd_gen_migration_phase' from x_table
where not exists (select 1 from x_table where name='d_gen_migration_phase');
update x_table set version='1.0' where name='d_gen_migration_phase';

create index d_gen_migration_phase_start_log_time_key
on d_gen_migration_phase (
	device_id,
	version,
	start_log_time
);
create index d_gen_migration_phase_end_log_time_key
on d_gen_migration_phase (
	device_id,
	version,
	end_log_time
);
