/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_orn_taxon_replace
(
	old_taxon			varchar(60) not null unique,
	new_taxon			varchar(60) not null,
	remark				varchar(500)
);
insert into x_table (name) select distinct 'r_orn_taxon_replace' from x_table
where not exists (select 1 from x_table where name='r_orn_taxon_replace');
update x_table set version='1.0' where name='r_orn_taxon_replace';
