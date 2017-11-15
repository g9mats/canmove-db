/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_beaufort
(
	beaufort_id			serial primary key,
	beaufort_no			integer not null unique,
	description			varchar(50) not null,
	wind_low			float not null,
	wind_high			float,
	wave_low			float not null,
	wave_high			float,
	sea_conditions		text,
	land_conditions		text
);
insert into x_table (name) select distinct 'r_beaufort' from x_table
where not exists (select 1 from x_table where name='r_beaufort');
update x_table set version='1.0' where name='r_beaufort';
