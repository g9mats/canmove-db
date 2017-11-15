/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_taxon_synonym
(
	tsn					integer not null,
	tsn_accepted		integer not null,
	update_date			date not null,
primary key (tsn, tsn_accepted)
);
insert into x_table (name) select distinct 'r_taxon_synonym' from x_table
where not exists (select 1 from x_table where name='r_taxon_synonym');
update x_table set version='1.0' where name='r_taxon_synonym';
