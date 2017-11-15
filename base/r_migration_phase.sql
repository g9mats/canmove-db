/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_migration_phase
(
	phase_id			serial primary key,
	phase_type			varchar(2) not null,
	description			varchar(20) not null,
	order_no			integer not null
);
insert into x_table (name) select distinct 'r_migration_phase' from x_table
where not exists (select 1 from x_table where name='r_migration_phase');
update x_table set version='1.0' where name='r_migration_phase';

create unique index r_migration_phase_type_key
on r_migration_phase (
	phase_type
);
