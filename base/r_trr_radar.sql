/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_trr_radar
(
	radar_id			serial primary key,
	radar_name			varchar(50) not null unique,
	remark				text,
unique (radar_name)
);
insert into x_table (name) select distinct 'r_trr_radar' from x_table
where not exists (select 1 from x_table where name='r_trr_radar');
update x_table set version='1.0' where name='r_trr_radar';
