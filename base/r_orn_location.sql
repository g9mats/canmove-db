/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_orn_location
(
	location_id			serial primary key,
	location			varchar(50) not null unique,
	latitude			float not null,
	longitude			float not null,
	altitude			float not null,
	declination			float not null,
	remark				text
);
insert into x_table (name) select distinct 'r_orn_location' from x_table
where not exists (select 1 from x_table where name='r_orn_location');
update x_table set version='1.0' where name='r_orn_location';
