/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_trr_track_type
(
	track_type_id		serial primary key,
	track_type			char(1) not null unique,
	description			varchar(10) not null
);
insert into x_table (name) select distinct 'r_trr_track_type' from x_table
where not exists (select 1 from x_table where name='r_trr_track_type');
update x_table set version='1.0' where name='r_trr_track_type';
