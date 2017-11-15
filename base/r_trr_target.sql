/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_trr_target
(
	target_id			serial primary key,
	target_type			char(1) not null unique,
	description			varchar(10) not null
);
insert into x_table (name) select distinct 'r_trr_target' from x_table
where not exists (select 1 from x_table where name='r_trr_target');
update x_table set version='1.0' where name='r_trr_target';
