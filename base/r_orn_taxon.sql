/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_orn_taxon
(
	species_no			integer not null unique,
	taxon				varchar(60) not null unique,
	english_name		varchar(30),
	swedish_name		varchar(30),
	remark				varchar(500)
);
insert into x_table (name) select distinct 'r_orn_taxon' from x_table
where not exists (select 1 from x_table where name='r_orn_taxon');
update x_table set version='1.0' where name='r_orn_taxon';
