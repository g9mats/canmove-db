/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_trr_site
(
	site_id				serial primary key,
	site_name			varchar(50) not null unique,
	latitude			float,
	longitude			float,
	remark				text
);
insert into x_table (name) select distinct 'r_trr_site' from x_table
where not exists (select 1 from x_table where name='r_trr_site');
update x_table set version='1.0' where name='r_trr_site';
