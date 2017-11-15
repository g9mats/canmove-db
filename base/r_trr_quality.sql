/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_trr_quality
(
	quality_id			serial primary key,
	quality_rating		char(1) not null unique,
	description			varchar(10) not null
);
insert into x_table (name) select distinct 'r_trr_quality' from x_table
where not exists (select 1 from x_table where name='r_trr_quality');
update x_table set version='1.0' where name='r_trr_quality';
